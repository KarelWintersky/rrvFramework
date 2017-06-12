<?php
/**
 * User: Arris
 * Date: 12.06.2017, time: 11:27
 */

if (!class_exists('websun')) require_once 'websun/websun.php';

/**
 * Системный юнит "Шаблон".
 * Содержит как обслуживающие функции по формированию данных для шаблона, так и сами данные.
 * Используется в resource-unit'ах для формирования представления (view).
 * По ряду причин не должен иметь статических методов.
 */
class rootTemplate {
    private $render_type;

    const ALLOWED_RENDERS = array('html', 'json', 'null');

    private $template_file;
    private $template_path;
    private $template_data;
    private $http_headers = array();
    private $http_status;

    /* ============= */
    public function __construct( $file , $path )
    {
        $this->template_file = $file;
        $this->template_path = $path;
        $this->data = array();
        $this->http_status = 200;
        $this->render_type = 'html';
    }

    public function setRender( $type = 'html' )
    {
        if ( in_array($type, $this::ALLOWED_RENDERS))
            $this->render_type = $type;
    }

    public function render()
    {
        if ($this->render_type === 'html') {
            return websun_parse_template_path( $this->template_data, $this->template_file, $this->template_path );
        } elseif ($this->render_type === 'json') {
            return json_encode( $this->template_data );
        } else return null;
    }

    public function __destruct()
    {
        if (!empty($this->http_headers)) {
            foreach ($this->http_headers as $h)
                header( $h );
        }
        echo $this->render();
        $this->setRender('null');
        exit;
    }

    public function setTemplatePath( $path )
    {
        $this->template_path = $path;
    }

    public function setTemplate( $file )
    {
        $this->template_file = $file;
    }

    public function set($path, $value)
    {
        $result = &$this->path_to_array( $path );

        if ($path != '/') {
            $result = $value;
        } else {
            if (!is_array($value)) {
                return false;
            } else {
                $result = array_merge_recursive($result, $value);
            }
        }
    }

    public function get( $path )
    {
        return $this->path_to_array( $path );
    }


    /* === PRIVATE === */

    /**
     *
     * @param $path
     * @return array
     */
    private function &path_to_array($path)
    {
        $path_array = explode('/', $path);
        $result = &$this->template_data;

        foreach ($path_array as $value) {
            if (!empty($value)) {
                if (!is_array($result)) {
                    $result[$value] = array();
                }
                $result =& $result[$value];
            }
        }
        return $result;
    }

    /* === TEST === */
    public function test()
    {
        return ( $this->template_data );
    }

    public function testget( $path )
    {
        var_dump( $this->get( $path ));
    }

    public function debug()
    {
        var_dump( 'Called debug() ');
        var_dump( $this->template_file );
        var_dump( $this->template_data );
        var_dump( $this->template_path );
    }

    public function sendHTTPHeader()
    {

    }

    public function setHTTPStatus( $status )
    {

    }

}
 
