<?php
namespace core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/17/17
 * @package charon
 */
class Template {

    /**
     * Outputs a template file
     * @param string $file
     * @param array $data
     */
    public static function output(string $file, array $data = []) {
        extract($data);
        @include(HTML . "/templates/$file");
    }

}