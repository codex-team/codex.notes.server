<?php

namespace App\System;


/**
 * Class Renderer
 * Provides template rendering methods
 *
 * @package App\System
 */
class Renderer
{
    /**
     * Return template with passed data
     * @param  string $tpl            - path to the template related to the current component\s tpl/ folder
     * @param  array|null $viewData   - data passed to the template
     * @return string
     */
    public static function render(string $tpl, array $viewData = null): string
    {
        /**
         * Get caller classname
         */
        $callerClass = debug_backtrace()[1]['class'];

        /**
         * Extract component name from string like 'App\Components\Index\Index'
         */
        $componentPath = explode('\\', $callerClass);
        $componentName = strtolower(array_pop($componentPath));

        /**
         * Compose template path
         */
        $tplDir = self::getTemplateDirectory($componentName);
        $tplPath = $tplDir . $tpl;

        /**
         * Render template content
         */
        $templateContent = self::renderTemplate($tplPath, $viewData);

        /**
         * Render layout with the template content
         */
        $layoutData = array_merge($viewData, ['content' => $templateContent]);
        return self::renderTemplate(self::getTemplateDirectory('base') . 'layout.php', $layoutData);

    }

    /**
     * Returns template forder path from the component
     * @param  string $componentName  - component name
     * @return string
     */
    private static  function getTemplateDirectory( string $componentName ): string
    {
        /**
         * Path to the /components directory
         */
        $componensDir = Config::DIR_COMPONENTS;

        /**
         * Path to the /tpl directory inside the component
         */
        return $componensDir . DIRECTORY_SEPARATOR . $componentName . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR;
    }

    /**
     * Renders template with data
     *
     * @param  string $file
     * @param  array $vars  - variables for the template
     * @return string
     */
    private function renderTemplate($file, array $vars = null): string
    {
        if (is_array($vars) && !empty($vars)) {
            extract($vars);
        }

        /**
         * Enable output bufferisation
         */
        ob_start();

        include PROJECTROOT . $file;

        return ob_get_clean();
    }
}