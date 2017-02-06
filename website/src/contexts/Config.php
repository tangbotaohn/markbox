<?php
/*
*--------------------------------------------------------------
* 配置管理,整个系统配置的获取修改与删除等操作
*--------------------------------------------------------------
* 最后修改时间 2012-1-8 Leon
* @version 1.1
* @author Leon(tmkook@gmail.com)
* @date 2010-2-27
*--------------------------------------------------------------
*/

namespace Markbox;

class Config
{
    private $_config = array();

    private $_dirs = array();

    public function __construct($dirs)
    {
        $this->_dirs = (array) $dirs;
    }

    /**
     * 获取配置项.
     *
     * @parame $path string 配置项索引格式:"文件/索引/索引..."
     *
     * @return data
     */
    public function get($path)
    {
        $path = $this->getKey($path);

        //没有加载
        if (!isset($this->_config[$path[0]])) {
            foreach ($this->_dirs as $dir) {
                $file = $dir.$path[0].'.php';
                if (file_exists($file)) {
                    $this->_config[$path[0]] = include_once $file;
                    break;
                } else {
                    $file = false;
                }
            }
            if (!$file) {
                throw new \Exception('不存在的配置文件');
            }
        }

        //返回配置项
        $conf = $this->_config[$path[0]];
        unset($path[0]);
        if (is_array($path) && !empty($path)) {
            foreach ($path as $key) {
                if (!isset($conf[$key])) {
                    return '';
                }
                $conf = $conf[$key];
            }
        }

        return $conf;
    }

    /**
     * 设置配置项.
     *
     * @parame $path string 要修改的配置项索引
     * @parame $value data 索引的值
     *
     * @return 无返回值
     */
    public function set($path, $value)
    {
        $path = $this->getKey($path);
        $set = &$this->_config[$path[0]];
        unset($path[0]);
        if (is_array($path) && !empty($path)) {
            foreach ($path as $key) {
                $set = &$set[$key];
            }
        }
        $set = $value;
    }

    /**
     * 删除配置项.
     *
     * @parame $path string 要删除的配置项索引
     *
     * @return 无返回值
     */
    public function remove($path)
    {
        $path = $this->getKey($path);
        $set = &$this->_config[$path[0]];
        unset($path[0]);
        if (is_array($path) && !empty($path)) {
            foreach ($path as $key) {
                $set = &$set[$key];
            }
        }
        $set = null;
        unset($set);
    }

    /**
     * 清除所有配置.
     *
     * @return 无返回值
     */
    public function clean()
    {
        unset($this->_config);
    }

    public function save($file)
    {
        $data = '<?php'."\r\n".'return '.var_export((array) $this->_config[$file], true).';';
        foreach ($this->_dirs as $dir) {
            $save_file = $dir.$file.'.php';
            if (file_exists($save_file)) {
                break;
            } else {
                $save_file = false;
            }
        }
        if ($save_file) {
            return file_put_contents($save_file, $data);
        } else {
            throw new \Exception('保存的的配置文件不存在');
        }
    }

    /**
     * 将路径解析成数组.
     *
     * @parame $path string 要解析的路径
     *
     * @return array
     */
    private function getKey($path)
    {
        $path = trim($path, '/');
        if (!empty($path)) {
            if (strpos($path, '/')) {
                $path = explode('/', $path);
            } else {
                $path = (array) $path;
            }
        }

        return $path;
    }
}

//end core/config.php
