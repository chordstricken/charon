/*
 *  jsmcrypt version 0.1  -  Copyright 2012 F. Doering
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307 USA
 */
 
 
 //this creates a static class mcrypt that is already initialized
 var mcrypt=mcrypt?mcrypt:new function(){
 
 //this allows the user to create instances of this class that keep
 //track of their own key, cipher, and mode
 //calling syntax becomes var myMcrypt=new mcrypt();
 //var mcrypt=function(){
  /**********
 * Private *
 **********/
 
 /************************
 * START OF CIPHER DEFFS *
 ************************/
 
 /* Cipher Data
 * This is an object, keyed with the cipher name whose value is an
 * array containing the number of octets (bytes) in the block size,
 * and the number of octets in the key.
 */
 var ciphers={		//	block size,	key size
  "rijndael-128"	:[	16,			32],
  "rijndael-192"	:[	24,			32],
  "rijndael-256"	:[	32,			32],
  "serpent"			:[	16,			32],
 }
 
 /* blockCipherCalls
 * This object is keyed by the cipher names and the vaules are
 * functions that calls external block ciphers to encypt or
 * decrypt a single block. These functions must have the arguments:
 * function(cipher_name,block,key,encrypt)
 * where: chipher_name is the text of the cipher name,
 * block is an array of inegers representing octets
 * key is a string
 * and encrypt indicates whether it should encrypt or decrypt
 * the block.
 * the function should modify the block as its output
 */
 var blockCipherCalls={};
 blockCipherCalls['rijndael-128']=function(cipher,block,key,encrypt){
	if(key.length<16)
	    key+=Array(17-key.length).join(String.fromCharCode(0));
	else if(key.length<24 && key.length>16)
	    key+=Array(25-key.length).join(String.fromCharCode(0));
	else if(key.length<32 && key.length>24)
	    key+=Array(33-key.length).join(String.fromCharCode(0));
	if(encrypt)
		Rijndael.Encrypt(block,key);
	else
		Rijndael.Decrypt(block,key);
	return block;
 };
 blockCipherCalls['rijndael-192']=blockCipherCalls['rijndael-128'];
 blockCipherCalls['rijndael-256']=blockCipherCalls['rijndael-128'];
 blockCipherCalls.serpent=function(cipher,block,key,encrypt){
	if(encrypt)
		Serpent.Encrypt(block);
	else
		Serpent.Decrypt(block);
	return block;
 };
 blockCipherCalls.serpent.init=function(cipher,key,encrypt){
	var keyA=[];
	for(var i=0;i<key.length;i++)
		keyA[i]=key.charCodeAt(i);
	Serpent.Init(keyA);
 };
 blockCipherCalls.serpent.deinit=function(cipher,key,encrypt){
	Serpent.Close();
 };
 
 /**********************
 * END OF CIPHER DEFFS *
 **********************/
 
 /*********
 * Public *
 *********/ 
 var pub={};
 
 /* Encrypt
 * This function encypts a plaintext message with an IV, key,  ciphertype, and mode
 * The message, key, and IV should be extended ascii strings
 * the ciphertype should be a string that is a supported cipher (see above)
 * the mode should be a string that is a supported mode of operation
 * the key, cipher type, and mode will default to the last used
 * these can be set without encypting by "encrypting" a null message
 */
 pub.Encrypt=function(message,IV,key, cipher, mode){
	return pub.Crypt(true,message,IV,key, cipher, mode);
};
/* Decrypt
 * See Encrypt for usage
 */ 
 
 pub.Decrypt=function(ctext,IV,key, cipher, mode){
	return pub.Crypt(false,ctext,IV,key, cipher, mode);
 };
/* Crypt
 * This function can encrypt or decrypt text
 */
 
pub.Crypt=function(encrypt,text,IV,key, cipher, mode){
	if(key) cKey=key; else key=cKey;
	if(cipher) cCipher=cipher; else cipher=cCipher;
	if(mode) cMode=mode; else mode=cMode;
	if(!text)
		return true;
	if(blockCipherCalls[cipher].init)
		blockCipherCalls[cipher].init(cipher,key,encrypt);
	var blockS=ciphers[cipher][0];
	var chunkS=blockS;
	var iv=new Array(blockS);
	switch(mode){
		case 'cfb':
			chunkS=1;//8-bit
		case 'cbc':
		case 'ncfb':
		case 'nofb':
		case 'ctr':
			if(!IV)
				throw "mcrypt.Crypt: IV Required for mode "+mode;
			if(IV.length!=blockS)
				throw "mcrypt.Crypt: IV must be "+blockS+" characters long for "+cipher;
			for(var i = blockS-1; i>=0; i--)
				iv[i] = IV.charCodeAt(i);
			break;
		case 'ecb':
			break;
		default:
			throw "mcrypt.Crypt: Unsupported mode of opperation"+cMode;
	}
	var chunks=Math.ceil(text.length/chunkS);
	var orig=text.length;
	text+=Array(chunks*chunkS-orig+1).join(String.fromCharCode(0));//zero pad the end
	var out='';
	switch(mode){
		case 'ecb':
			for(var i = 0; i < chunks; i++){
				for(var j = 0; j < chunkS; j++)
					iv[j]=text.charCodeAt((i*chunkS)+j);
				blockCipherCalls[cipher](cipher,iv, cKey,encrypt);
				for(var j = 0; j < chunkS; j++)
					out+=String.fromCharCode(iv[j]);
			}
			break;
		case 'cbc':
			if(encrypt){
				for(var i = 0; i < chunks; i++){
					for(var j = 0; j < chunkS; j++)
						iv[j]=text.charCodeAt((i*chunkS)+j)^iv[j];
					blockCipherCalls[cipher](cipher,iv, cKey,true);
					for(var j = 0; j < chunkS; j++)
						out+=String.fromCharCode(iv[j]);
				}
			}
			else{
				for(var i = 0; i < chunks; i++){
					var temp=iv;
						iv=new Array(chunkS);
					for(var j = 0; j < chunkS; j++)
						iv[j]=text.charCodeAt((i*chunkS)+j);
					var decr=iv.slice(0);
					blockCipherCalls[cipher](cipher,decr, cKey,false);
					for(var j = 0; j < chunkS; j++)
						out+=String.fromCharCode(temp[j]^decr[j]);
				}
			}
			break;
		case 'cfb':
			for(var i = 0; i < chunks; i++){
				var temp=iv.slice(0);
				blockCipherCalls[cipher](cipher,temp, cKey,true);
				temp=temp[0]^text.charCodeAt(i);
				iv.push(encrypt?temp:text.charCodeAt(i));
				iv.shift();
				out+=String.fromCharCode(temp);
			}
			out=out.substr(0,orig);
			break;
		case 'ncfb':
			for(var i = 0; i < chunks; i++){
				blockCipherCalls[cipher](cipher,iv, cKey,true);
				for(var j = 0; j < chunkS; j++){
					var temp=text.charCodeAt((i*chunkS)+j);
					iv[j]=temp^iv[j];
					out+=String.fromCharCode(iv[j]);
					if(!encrypt)
						iv[j]=temp;
				}
			}
			out=out.substr(0,orig);
			break;
		case 'nofb':
			for(var i = 0; i < chunks; i++){
				blockCipherCalls[cipher](cipher,iv, cKey,true);
				for(var j = 0; j < chunkS; j++)
					out+=String.fromCharCode(text.charCodeAt((i*chunkS)+j)^iv[j]);
			}
			out=out.substr(0,orig);
			break;
		case 'ctr':
			for(var i = 0; i < chunks; i++){
				temp=iv.slice(0);
				blockCipherCalls[cipher](cipher,temp, cKey,true);
				for(var j = 0; j < chunkS; j++)
					out+=String.fromCharCode(text.charCodeAt((i*chunkS)+j)^temp[j]);
				var carry=1;
				var index=chunkS;
				do{
					index--;
					iv[index]+=1;
					carry=iv[index]>>8;
					iv[index]&=255;
				}while(carry)
			}
			out=out.substr(0,orig);
			break;
	}
	if(blockCipherCalls[cipher].deinit)
		blockCipherCalls[cipher].deinit(cipher,key,encrypt);
	return out;
};

//Gets the block size of the specified cipher
pub.get_block_size=function(cipher,mode){
	if(!cipher) cipher=cCipher;
	if(!ciphers[cipher])
		return false;
	return ciphers[cipher][0];
}

//Gets the name of the specified cipher
pub.get_cipher_name=function(cipher){
	if(!cipher) cipher=cCipher;
	if(!ciphers[cipher])
		return false;
	return cipher;
}

//Returns the size of the IV belonging to a specific cipher/mode combination
pub.get_iv_size=function(cipher,mode){
	if(!cipher) cipher=cCipher;
	if(!ciphers[cipher])
		return false;
	return ciphers[cipher][0];
}

//Gets the key size of the specified cipher
pub.get_key_size=function(cipher,mode){
	if(!cipher) cipher=cCipher;
	if(!ciphers[cipher])
		return false;
	return ciphers[cipher][1];
}

//Gets an array of all supported ciphers
pub.list_algorithms=function(){
	var ret=[];
	for(var i in ciphers)
		ret.push(i);
	return ret;
}

pub.list_modes=function(){
	return ['ecb','cbc','cfb','ncfb','nofb','ctr'];
}


 
 /**********
 * Private *
 **********/
  
 var cMode='cbc';
 var cCipher='rijndael-128';
 var cKey='12345678911234567892123456789312';


return pub; 
};
/*
 *  jsaes version 0.1  -  Copyright 2006 B. Poettering
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307 USA
 */
var Rijndael=Rijndael?Rijndael: new function(){
/*
 *
 * This is a javascript implementation of the rijndael block cipher. Key lengths 
 * of 128, 192 and 256 bits, and block lengths of 128, 192, and 256 bit in any
 * combination are supported.
 *
 * The well-functioning of the encryption/decryption routines has been 
 * verified for different key lengths with the test vectors given in 
 * FIPS-197, Appendix C.
 *
 * The following code example enciphers the plaintext block '00 11 22 .. EE FF'
 * with the 256 bit key '12345678911234567892123456789312'.
 *
 *    var block = new Array(16);
 *    for(var i = 0; i < 16; i++)
 *        block[i] = 0x11 * i;
 *
 *    var key = '12345678911234567892123456789312';
 *
 *    rijndael.Encrypt(block, key);
 *
 */

/******************************************************************************/

//public
var pub={};

pub.Encrypt=function(block, key) {
	crypt(block,key,true);
}
pub.Decrypt=function(block, key) {
	crypt(block,key,false);
}

//private

var sizes=[16,24,32];

	//key bytes	16,	24,	32		block bytes
var rounds=[[	10,	12,	14],//	16
			[	12,	12,	14],//	24
			[	14,	14,	14]];//	32

var expandedKeys={};//object to keep keys we've already expanded in.

var ExpandKey=function(key) {
  if(!expandedKeys[key]){
	  var kl = key.length, ks, Rcon = 1;
	  ks=15<<5;
	  keyA=new Array(ks);
	  for(var i = 0; i < kl; i++)
		keyA[i]=key.charCodeAt(i);
	  for(var i = kl; i < ks; i += 4) {
		var temp = keyA.slice(i - 4, i);
		if (i % kl == 0) {
		  temp = [	Sbox[temp[1]] ^ Rcon,	Sbox[temp[2]], 
					Sbox[temp[3]], 			Sbox[temp[0]]]; 
		  if ((Rcon <<= 1) >= 256)
			Rcon ^= 0x11b;
		}
		else if ((kl > 24) && (i % kl == 16))
		  temp = [Sbox[temp[0]], Sbox[temp[1]], 
				  Sbox[temp[2]], Sbox[temp[3]]];       
		for(var j = 0; j < 4; j++)
		  keyA[i+j] = keyA[ i+j-kl ] ^ temp[j];
	  }
	  expandedKeys[key]=keyA;
  }
  return expandedKeys[key];
}

var crypt=function(block, key,encrypt) {
  var bB=block.length;
  var kB = key.length;
  var bBi=0;
  var kBi=0;
  switch(bB){
	case 32:bBi++;
	case 24:bBi++;
	case 16:break;
	default: throw 'rijndael: Unsupported block size: '+block.length;
  }
  switch(kB){
	case 32:kBi++;
	case 24:kBi++;
	case 16:break;
	default: throw 'rijndael: Unsupported key size: '+key.length;
  }
  var r=rounds[bBi][kBi];
  key=ExpandKey(key);
  var end=r*bB;
  if(encrypt){
	  AddRoundKey(block, key.slice(0, bB));
	  var SRT=ShiftRowTab[bBi];
	  for(var i = bB; i < end; i += bB) {
		SubBytes(block, Sbox);
		ShiftRows(block, SRT);
		MixColumns(block);
		AddRoundKey(block, key.slice(i, i + bB));
	  }
	  SubBytes(block, Sbox);
	  ShiftRows(block, SRT);
	  AddRoundKey(block, key.slice(i, i+bB));
  }
  else{ //decrypt
	  AddRoundKey(block, key.slice(end, end+bB));
	  var SRT=ShiftRowTab_Inv[bBi];
	  ShiftRows(block, SRT);
	  SubBytes(block, Sbox_Inv);
	  for(var i = end-bB; i >= bB; i -= bB) {
		AddRoundKey(block, key.slice(i, i + bB));
		MixColumns_Inv(block);
		ShiftRows(block, SRT);
		SubBytes(block, Sbox_Inv);
	  }
	  AddRoundKey(block, key.slice(0, bB));
  }
}
/******************************************************************************/

/* The following lookup tables and functions are for internal use only! */
var Sbox = new Array(99,124,119,123,242,107,111,197,48,1,103,43,254,215,171,
  118,202,130,201,125,250,89,71,240,173,212,162,175,156,164,114,192,183,253,
  147,38,54,63,247,204,52,165,229,241,113,216,49,21,4,199,35,195,24,150,5,154,
  7,18,128,226,235,39,178,117,9,131,44,26,27,110,90,160,82,59,214,179,41,227,
  47,132,83,209,0,237,32,252,177,91,106,203,190,57,74,76,88,207,208,239,170,
  251,67,77,51,133,69,249,2,127,80,60,159,168,81,163,64,143,146,157,56,245,
  188,182,218,33,16,255,243,210,205,12,19,236,95,151,68,23,196,167,126,61,
  100,93,25,115,96,129,79,220,34,42,144,136,70,238,184,20,222,94,11,219,224,
  50,58,10,73,6,36,92,194,211,172,98,145,149,228,121,231,200,55,109,141,213,
  78,169,108,86,244,234,101,122,174,8,186,120,37,46,28,166,180,198,232,221,
  116,31,75,189,139,138,112,62,181,102,72,3,246,14,97,53,87,185,134,193,29,
  158,225,248,152,17,105,217,142,148,155,30,135,233,206,85,40,223,140,161,
  137,13,191,230,66,104,65,153,45,15,176,84,187,22);
		//row	0	1	2	3		block Bytes
var rowshifts=[[0,	1,	2,	3],		//16
			   [0,	1,	2,	3],		//24
			   [0,	1,	3,	4]];	//32

var ShiftRowTab = Array(3);
for(var i=0;i<3;i++){
	ShiftRowTab[i]=Array(sizes[i]);
	for(var j=sizes[i];j>=0;j--)
		ShiftRowTab[i][j]=(j+(rowshifts[i][j&3]<<2))%sizes[i];
}
var Sbox_Inv = new Array(256);
  for(var i = 0; i < 256; i++)
    Sbox_Inv[Sbox[i]] = i;
var ShiftRowTab_Inv = Array(3);
for(var i=0;i<3;i++){
	ShiftRowTab_Inv[i]=Array(sizes[i]);
	for(var j=sizes[i];j>=0;j--)
		ShiftRowTab_Inv[i][ShiftRowTab[i][j]]=j;
}
var xtime = new Array(256);
for(var i = 0; i < 128; i++) {
	xtime[i] = i << 1;
	xtime[128 + i] = (i << 1) ^ 0x1b;
}

var SubBytes=function(state, sbox) {
  for(var i = state.length-1; i>=0; i--)
    state[i] = sbox[state[i]];  
}

var AddRoundKey=function (state, rkey) {
  for(var i=state.length-1 ; i >=0 ; i--)
    state[i] ^= rkey[i];
}

var ShiftRows=function(state, shifttab) {
  var h = state.slice(0);
  for(var i = state.length-1 ; i >=0; i--)
    state[i] = h[shifttab[i]];
}

var MixColumns= function(state) {
  for(var i = state.length-4; i >=0; i -= 4) {
    var s0 = state[i + 0], s1 = state[i + 1];
    var s2 = state[i + 2], s3 = state[i + 3];
    var h = s0 ^ s1 ^ s2 ^ s3;
    state[i + 0] ^= h ^ xtime[s0 ^ s1];
    state[i + 1] ^= h ^ xtime[s1 ^ s2];
    state[i + 2] ^= h ^ xtime[s2 ^ s3];
    state[i + 3] ^= h ^ xtime[s3 ^ s0];
  }
}

var MixColumns_Inv=function(state) {
  for(var i = state.length-4; i >=0; i -= 4) {
    var s0 = state[i + 0], s1 = state[i + 1];
    var s2 = state[i + 2], s3 = state[i + 3];
    var h = s0 ^ s1 ^ s2 ^ s3;
    var xh = xtime[h];
    var h1 = xtime[xtime[xh ^ s0 ^ s2]] ^ h;
    var h2 = xtime[xtime[xh ^ s1 ^ s3]] ^ h;
    state[i + 0] ^= h1 ^ xtime[s0 ^ s1];
    state[i + 1] ^= h2 ^ xtime[s1 ^ s2];
    state[i + 2] ^= h1 ^ xtime[s2 ^ s3];
    state[i + 3] ^= h2 ^ xtime[s3 ^ s0];
  }
}
return pub;
};
function md5cycle(x, k) {
var a = x[0], b = x[1], c = x[2], d = x[3];

a = ff(a, b, c, d, k[0], 7, -680876936);
d = ff(d, a, b, c, k[1], 12, -389564586);
c = ff(c, d, a, b, k[2], 17,  606105819);
b = ff(b, c, d, a, k[3], 22, -1044525330);
a = ff(a, b, c, d, k[4], 7, -176418897);
d = ff(d, a, b, c, k[5], 12,  1200080426);
c = ff(c, d, a, b, k[6], 17, -1473231341);
b = ff(b, c, d, a, k[7], 22, -45705983);
a = ff(a, b, c, d, k[8], 7,  1770035416);
d = ff(d, a, b, c, k[9], 12, -1958414417);
c = ff(c, d, a, b, k[10], 17, -42063);
b = ff(b, c, d, a, k[11], 22, -1990404162);
a = ff(a, b, c, d, k[12], 7,  1804603682);
d = ff(d, a, b, c, k[13], 12, -40341101);
c = ff(c, d, a, b, k[14], 17, -1502002290);
b = ff(b, c, d, a, k[15], 22,  1236535329);

a = gg(a, b, c, d, k[1], 5, -165796510);
d = gg(d, a, b, c, k[6], 9, -1069501632);
c = gg(c, d, a, b, k[11], 14,  643717713);
b = gg(b, c, d, a, k[0], 20, -373897302);
a = gg(a, b, c, d, k[5], 5, -701558691);
d = gg(d, a, b, c, k[10], 9,  38016083);
c = gg(c, d, a, b, k[15], 14, -660478335);
b = gg(b, c, d, a, k[4], 20, -405537848);
a = gg(a, b, c, d, k[9], 5,  568446438);
d = gg(d, a, b, c, k[14], 9, -1019803690);
c = gg(c, d, a, b, k[3], 14, -187363961);
b = gg(b, c, d, a, k[8], 20,  1163531501);
a = gg(a, b, c, d, k[13], 5, -1444681467);
d = gg(d, a, b, c, k[2], 9, -51403784);
c = gg(c, d, a, b, k[7], 14,  1735328473);
b = gg(b, c, d, a, k[12], 20, -1926607734);

a = hh(a, b, c, d, k[5], 4, -378558);
d = hh(d, a, b, c, k[8], 11, -2022574463);
c = hh(c, d, a, b, k[11], 16,  1839030562);
b = hh(b, c, d, a, k[14], 23, -35309556);
a = hh(a, b, c, d, k[1], 4, -1530992060);
d = hh(d, a, b, c, k[4], 11,  1272893353);
c = hh(c, d, a, b, k[7], 16, -155497632);
b = hh(b, c, d, a, k[10], 23, -1094730640);
a = hh(a, b, c, d, k[13], 4,  681279174);
d = hh(d, a, b, c, k[0], 11, -358537222);
c = hh(c, d, a, b, k[3], 16, -722521979);
b = hh(b, c, d, a, k[6], 23,  76029189);
a = hh(a, b, c, d, k[9], 4, -640364487);
d = hh(d, a, b, c, k[12], 11, -421815835);
c = hh(c, d, a, b, k[15], 16,  530742520);
b = hh(b, c, d, a, k[2], 23, -995338651);

a = ii(a, b, c, d, k[0], 6, -198630844);
d = ii(d, a, b, c, k[7], 10,  1126891415);
c = ii(c, d, a, b, k[14], 15, -1416354905);
b = ii(b, c, d, a, k[5], 21, -57434055);
a = ii(a, b, c, d, k[12], 6,  1700485571);
d = ii(d, a, b, c, k[3], 10, -1894986606);
c = ii(c, d, a, b, k[10], 15, -1051523);
b = ii(b, c, d, a, k[1], 21, -2054922799);
a = ii(a, b, c, d, k[8], 6,  1873313359);
d = ii(d, a, b, c, k[15], 10, -30611744);
c = ii(c, d, a, b, k[6], 15, -1560198380);
b = ii(b, c, d, a, k[13], 21,  1309151649);
a = ii(a, b, c, d, k[4], 6, -145523070);
d = ii(d, a, b, c, k[11], 10, -1120210379);
c = ii(c, d, a, b, k[2], 15,  718787259);
b = ii(b, c, d, a, k[9], 21, -343485551);

x[0] = add32(a, x[0]);
x[1] = add32(b, x[1]);
x[2] = add32(c, x[2]);
x[3] = add32(d, x[3]);

}

function cmn(q, a, b, x, s, t) {
a = add32(add32(a, q), add32(x, t));
return add32((a << s) | (a >>> (32 - s)), b);
}

function ff(a, b, c, d, x, s, t) {
return cmn((b & c) | ((~b) & d), a, b, x, s, t);
}

function gg(a, b, c, d, x, s, t) {
return cmn((b & d) | (c & (~d)), a, b, x, s, t);
}

function hh(a, b, c, d, x, s, t) {
return cmn(b ^ c ^ d, a, b, x, s, t);
}

function ii(a, b, c, d, x, s, t) {
return cmn(c ^ (b | (~d)), a, b, x, s, t);
}

function md51(s) {
txt = '';
var n = s.length,
state = [1732584193, -271733879, -1732584194, 271733878], i;
for (i=64; i<=s.length; i+=64) {
md5cycle(state, md5blk(s.substring(i-64, i)));
}
s = s.substring(i-64);
var tail = [0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0];
for (i=0; i<s.length; i++)
tail[i>>2] |= s.charCodeAt(i) << ((i%4) << 3);
tail[i>>2] |= 0x80 << ((i%4) << 3);
if (i > 55) {
md5cycle(state, tail);
for (i=0; i<16; i++) tail[i] = 0;
}
tail[14] = n*8;
md5cycle(state, tail);
return state;
}

/* there needs to be support for Unicode here,
 * unless we pretend that we can redefine the MD-5
 * algorithm for multi-byte characters (perhaps
 * by adding every four 16-bit characters and
 * shortening the sum to 32 bits). Otherwise
 * I suggest performing MD-5 as if every character
 * was two bytes--e.g., 0040 0025 = @%--but then
 * how will an ordinary MD-5 sum be matched?
 * There is no way to standardize text to something
 * like UTF-8 before transformation; speed cost is
 * utterly prohibitive. The JavaScript standard
 * itself needs to look at this: it should start
 * providing access to strings as preformed UTF-8
 * 8-bit unsigned value arrays.
 */
function md5blk(s) { /* I figured global was faster.   */
var md5blks = [], i; /* Andy King said do it this way. */
for (i=0; i<64; i+=4) {
md5blks[i>>2] = s.charCodeAt(i)
+ (s.charCodeAt(i+1) << 8)
+ (s.charCodeAt(i+2) << 16)
+ (s.charCodeAt(i+3) << 24);
}
return md5blks;
}

var hex_chr = '0123456789abcdef'.split('');

function rhex(n)
{
var s='', j=0;
for(; j<4; j++)
s += hex_chr[(n >> (j * 8 + 4)) & 0x0F]
+ hex_chr[(n >> (j * 8)) & 0x0F];
return s;
}

function hex(x) {
for (var i=0; i<x.length; i++)
x[i] = rhex(x[i]);
return x.join('');
}

function md5(s) {
return hex(md51(s));
}

/* this function is much faster,
so if possible we use it. Some IEs
are the only ones I know of that
need the idiotic second function,
generated by an if clause.  */

function add32(a, b) {
return (a + b) & 0xFFFFFFFF;
}

if (md5('hello') != '5d41402abc4b2a76b9719d911017c592') {
function add32(x, y) {
var lsw = (x & 0xFFFF) + (y & 0xFFFF),
msw = (x >> 16) + (y >> 16) + (lsw >> 16);
return (msw << 16) | (lsw & 0xFFFF);
}
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
/*  SHA-256 implementation in JavaScript                (c) Chris Veness 2002-2014 / MIT Licence  */
/*                                                                                                */
/*  - see http://csrc.nist.gov/groups/ST/toolkit/secure_hashing.html                              */
/*        http://csrc.nist.gov/groups/ST/toolkit/examples.html                                    */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

/* jshint node:true *//* global define, escape, unescape */
'use strict';


/**
 * SHA-256 hash function reference implementation.
 *
 * @namespace
 */
var Sha256 = {};


/**
 * Generates SHA-256 hash of string.
 *
 * @param   {string} msg - String to be hashed
 * @returns {string} Hash of msg as hex character string
 */
Sha256.hash = function(msg) {
    // convert string to UTF-8, as SHA only deals with byte-streams
    msg = msg.utf8Encode();
    
    // constants [§4.2.2]
    var K = [
        0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
        0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
        0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
        0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
        0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
        0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
        0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
        0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2 ];
    // initial hash value [§5.3.1]
    var H = [
        0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19 ];

    // PREPROCESSING 
 
    msg += String.fromCharCode(0x80);  // add trailing '1' bit (+ 0's padding) to string [§5.1.1]

    // convert string msg into 512-bit/16-integer blocks arrays of ints [§5.2.1]
    var l = msg.length/4 + 2; // length (in 32-bit integers) of msg + ‘1’ + appended length
    var N = Math.ceil(l/16);  // number of 16-integer-blocks required to hold 'l' ints
    var M = new Array(N);

    for (var i=0; i<N; i++) {
        M[i] = new Array(16);
        for (var j=0; j<16; j++) {  // encode 4 chars per integer, big-endian encoding
            M[i][j] = (msg.charCodeAt(i*64+j*4)<<24) | (msg.charCodeAt(i*64+j*4+1)<<16) | 
                      (msg.charCodeAt(i*64+j*4+2)<<8) | (msg.charCodeAt(i*64+j*4+3));
        } // note running off the end of msg is ok 'cos bitwise ops on NaN return 0
    }
    // add length (in bits) into final pair of 32-bit integers (big-endian) [§5.1.1]
    // note: most significant word would be (len-1)*8 >>> 32, but since JS converts
    // bitwise-op args to 32 bits, we need to simulate this by arithmetic operators
    M[N-1][14] = ((msg.length-1)*8) / Math.pow(2, 32); M[N-1][14] = Math.floor(M[N-1][14]);
    M[N-1][15] = ((msg.length-1)*8) & 0xffffffff;


    // HASH COMPUTATION [§6.1.2]

    var W = new Array(64); var a, b, c, d, e, f, g, h;
    for (var i=0; i<N; i++) {

        // 1 - prepare message schedule 'W'
        for (var t=0;  t<16; t++) W[t] = M[i][t];
        for (var t=16; t<64; t++) W[t] = (Sha256.σ1(W[t-2]) + W[t-7] + Sha256.σ0(W[t-15]) + W[t-16]) & 0xffffffff;

        // 2 - initialise working variables a, b, c, d, e, f, g, h with previous hash value
        a = H[0]; b = H[1]; c = H[2]; d = H[3]; e = H[4]; f = H[5]; g = H[6]; h = H[7];

        // 3 - main loop (note 'addition modulo 2^32')
        for (var t=0; t<64; t++) {
            var T1 = h + Sha256.Σ1(e) + Sha256.Ch(e, f, g) + K[t] + W[t];
            var T2 =     Sha256.Σ0(a) + Sha256.Maj(a, b, c);
            h = g;
            g = f;
            f = e;
            e = (d + T1) & 0xffffffff;
            d = c;
            c = b;
            b = a;
            a = (T1 + T2) & 0xffffffff;
        }
         // 4 - compute the new intermediate hash value (note 'addition modulo 2^32')
        H[0] = (H[0]+a) & 0xffffffff;
        H[1] = (H[1]+b) & 0xffffffff; 
        H[2] = (H[2]+c) & 0xffffffff; 
        H[3] = (H[3]+d) & 0xffffffff; 
        H[4] = (H[4]+e) & 0xffffffff;
        H[5] = (H[5]+f) & 0xffffffff;
        H[6] = (H[6]+g) & 0xffffffff; 
        H[7] = (H[7]+h) & 0xffffffff; 
    }

    return Sha256.toHexStr(H[0]) + Sha256.toHexStr(H[1]) + Sha256.toHexStr(H[2]) + Sha256.toHexStr(H[3]) + 
           Sha256.toHexStr(H[4]) + Sha256.toHexStr(H[5]) + Sha256.toHexStr(H[6]) + Sha256.toHexStr(H[7]);
};


/**
 * Rotates right (circular right shift) value x by n positions [§3.2.4].
 * @private
 */
Sha256.ROTR = function(n, x) {
    return (x >>> n) | (x << (32-n));
};

/**
 * Logical functions [§4.1.2].
 * @private
 */
Sha256.Σ0  = function(x) { return Sha256.ROTR(2,  x) ^ Sha256.ROTR(13, x) ^ Sha256.ROTR(22, x); };
Sha256.Σ1  = function(x) { return Sha256.ROTR(6,  x) ^ Sha256.ROTR(11, x) ^ Sha256.ROTR(25, x); };
Sha256.σ0  = function(x) { return Sha256.ROTR(7,  x) ^ Sha256.ROTR(18, x) ^ (x>>>3);  };
Sha256.σ1  = function(x) { return Sha256.ROTR(17, x) ^ Sha256.ROTR(19, x) ^ (x>>>10); };
Sha256.Ch  = function(x, y, z) { return (x & y) ^ (~x & z); };
Sha256.Maj = function(x, y, z) { return (x & y) ^ (x & z) ^ (y & z); };


/**
 * Hexadecimal representation of a number.
 * @private
 */
Sha256.toHexStr = function(n) {
    // note can't use toString(16) as it is implementation-dependant,
    // and in IE returns signed numbers when used on full words
    var s="", v;
    for (var i=7; i>=0; i--) { v = (n>>>(i*4)) & 0xf; s += v.toString(16); }
    return s;
};


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */


/** Extend String object with method to encode multi-byte string to utf8
 *  - monsur.hossa.in/2012/07/20/utf-8-in-javascript.html */
if (typeof String.prototype.utf8Encode == 'undefined') {
    String.prototype.utf8Encode = function() {
        return unescape( encodeURIComponent( this ) );
    };
}

/** Extend String object with method to decode utf8 string to multi-byte */
if (typeof String.prototype.utf8Decode == 'undefined') {
    String.prototype.utf8Decode = function() {
        try {
            return decodeURIComponent( escape( this ) );
        } catch (e) {
            return this; // invalid UTF-8? return as-is
        }
    };
}


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
if (typeof module != 'undefined' && module.exports) module.exports = Sha256; // CommonJs export
if (typeof define == 'function' && define.amd) define([], function() { return Sha256; }); // AMD

function sha256(param) {
    return Sha256.hash(param);
}
/**
 * Stores a cookie
 * @param string key
 * @param mixed val
 * @param int days (ttl)
 */
function set_cookie(key, val, days)
{
    var exdate = new Date();
    days = days !== undefined ? days : 7;
    exdate.setDate(exdate.getDate() + days);
    document.cookie = key + "=" + encodeURIComponent(val) + "; expires=" + exdate.toUTCString() + ";path=/";
}

/**
 * Retrieves a cookie
 * @param  string key_param
 * @return mixed
 */
function get_cookie(key)
{
    var cookies = document.cookie.split(';'), i;
    for (i in cookies) {
        var split = cookies[i].split('=');
        if (split[0].trim() == key) {
            return decodeURIComponent(split[1]);
        }
    }
    return null;
}

/**
 * Encrypts a string
 * @param str
 * @param key
 */
function encrypt(str, key) {
    var iv  = new Date().getTime() + ""; // create iv
    iv  = md5(iv);
    key = md5(key); // hash password
    str = JSON.stringify(str); // encode the param

    var crypt = mcrypt.Encrypt(str, iv, key, 'rijndael-256', 'cbc');

    return {
        crypt: btoa(crypt),
        iv: btoa(iv)
    };
}

/**
 * Decrypts a string
 * @param str
 * @param key
 */
function decrypt(obj, key) {
    obj = typeof(obj) == 'object' ? obj : JSON.parse(obj);
    var dec = mcrypt.Decrypt(atob(obj.crypt), atob(obj.iv), md5(key), 'rijndael-256', 'cbc').trim();
    
    // mcrypt pads the decryption with null bytes. Trim them off
    return JSON.parse(dec.replace(/\x00*$/, ''));
}

/**
 * Generates a unique id
 * @returns {number}
 */
function unique_id() {
    return _uid++;
}
var _uid = new Date().getTime();
