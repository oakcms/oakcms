<?php

namespace YOOtheme\Framework\View\Asset\Filter;

use YOOtheme\Framework\View\Asset\AssetInterface;

class CssImportResolverFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterContent(AssetInterface $asset)
    {
        // has path?
        if (!$path = $asset->getOption('path')) {
            return;
        }

        // resolve @import rules
        $content = $this->load($path, $asset->getContent());

        // move unresolved @import rules to the top
        $comments = array();
        $regexp   = '/@import[^;]+;/i';
        $content  = $this->replaceComments($content, $comments);

        if (preg_match_all($regexp, $content, $matches)) {
            $content = preg_replace($regexp, '', $content);
            $content = implode("\n", $matches[0])."\n".$content;
        }

        $content = $this->restoreComments($content, $comments);

        $asset->setContent($content);
    }

    /**
     * Load file and get it's content.
     *
     * @param  string $file
     * @param  string $content
     * @return string
     */
    protected function load($file, $content = '')
    {
        static $path;

        $oldpath = $path;

        if ($path && !strpos($file, '://')) {
            $file = realpath($path.'/'.$file);
        }

        $path = dirname($file);

        // get content from file, if not already set
        if (!$content && file_exists($file)) {
            $content = @file_get_contents($file);
        }

        $comments = array();

        // remove multiple charset declarations and resolve @imports to its actual content
        if ($content) {
            $content = $this->replaceComments($content, $comments);
            $content = preg_replace('/^@charset\s+[\'"](\S*)\b[\'"];/i', '', $content);
            $content = preg_replace_callback('/@import\s*(?:url\(\s*)?[\'"]?(?![a-z]+:)([^\'"\()]+)[\'"]?\s*\)?\s*;/', array($this, 'loadRecursive'), $content);
            $content = $this->restoreComments($content, $comments);
        }

        $path = $oldpath;

        return $content;
    }

    /**
     * Load file recursively and fix url paths.
     *
     * @param  array $matches
     * @return string
     */
    protected function loadRecursive($matches)
    {
        // resolve @import rules recursively
        $file = $this->load($matches[1]);

        // get file's directory remove '.' if its the current directory
        $directory = dirname($matches[1]);
        $directory = $directory == '.' ? '' : $directory.'/';

        // add directory file's to urls paths
        return preg_replace('/url\s*\(([\'"]?)(?![a-z]+:|\/+)/i', 'url(\1'.$directory, $file);
    }

    /**
     * Replace all comments with a placeholder.
     *
     * @param  string $content
     * @param  array  $comments
     * @return array
     */
    protected function replaceComments($content, &$comments)
    {
        $callback = function ($matches) use (&$comments) {

            $key            = sprintf('[%s]', md5($matches[0]));
            $comments[$key] = $matches[0];

            return $key;
        };

        return preg_replace_callback('/\/\*(?s:.)*?\*\/|((?!:).)?\/\/.*/', $callback, $content);
    }

    /**
     * Restore all comments and replace the placeholders with the actual comment.
     *
     * @param  string $content
     * @param  array  $comments
     * @return string
     */
    protected function restoreComments($content, array $comments)
    {
        foreach ($comments as $key => $comment) {
            $content = str_replace($key, $comment, $content);
        }

        return $content;
    }
}
