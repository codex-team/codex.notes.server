<?php

namespace App\System;

use App\System\Utilities\Base;
use \App\System\Utilities\Config;

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
    public static function render(string $tpl, array $viewData = null): void
    {
        /**
         * Get caller classname
         */
        $callerClass = debug_backtrace()[1]['class'];

        /**
         * Extract component name from string like 'App\Components\Index\Index'
         */
        $componentName = strtolower(array_pop(explode('\\', $callerClass)));

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
        echo self::renderTemplate(self::getTemplateDirectory('global') . 'layout.php', $layoutData);

    }

    /**
     * Returns template forder path from the component
     * @param  string $componentName  - component name
     * @return string
     */
    private function getTemplateDirectory( string $componentName ): string
    {
        /**
         * Path to the /components directory
         */
        $componensDir = Config::getPathTo(Base::DIR_COMPONENTS);

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

        include $file;

        return ob_get_clean();
    }
}