<?php

    /**
     * Информационная панель
     *
     * @author Zmi
     */
    class Debug_DebugPanel
    {
        const PHP_INI_USER   = 1;
        const PHP_INI_PERDIR = 2;
        const PHP_INI_SYSTEM = 4;
        const PHP_INI_ALL    = 7;

        public function Files()
        {
            $files  = get_included_files();
            $stat   = array();
            inc('Debug_FileSys');
            foreach ($files as $file)
            {
                $stat[] = array('file'  => $file,
                                'size'  => Debug_FileSys::Size($file),
                                'lines' => count(file($file)));
            }
            return $stat;
        }

        public function TotalFileSize()
        {
            $total = 0;
            foreach (get_included_files() as $f)
            {
                $total += filesize($f);
            }
            $size         = sprintf("%u", $total);
            $filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
            return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
        }

        public function TotalFileLines()
        {
            $total = 0;
            foreach (get_included_files() as $f)
            {
                $total += count(file($f));
            }
            return $total;
        }

        public function Db()
        {
            return class_exists('MyDataBaseLog') ? MyDataBaseLog::Render() : '';
        }

        public function MemoryUsage($memory) // TODO: Память должна замеряться при инициализации, чтобы узнать сколько весит php скрипт при запуске, затем должно определиться сколько памяти занимает дебагер (вместе с перехватом запросов, вообще стоит настроить его так чтобы можно было вычитать любые суммы). Вывести должно так: <чистый скрипт> (+запуск PHP) / <весь скрипт> / <реально занимаемая память>
        {
            $size         = sprintf("%u", $memory-133120);
            $filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
            $memory0 = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';

            $difference = 108738; // это 106.19 кбайт // 236.78 - использует дебагер, а 130.59 - запущенный PHP, так что чтобы вычесть вес чистого дебагера берем разницу этих значений
            $size         = sprintf("%u", $memory);
            $filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
            $memory1 = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';

            $size         = sprintf("%u", memory_get_usage(true));
            $filesizename = array(" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb");
            $memory2 = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';

            return ($memory0 . ' / ' . $memory1 . ' / ' . $memory2);
        }

        public static function ShowPhpIniAccess($access)
        {
            switch ($access)
            {
                case self::PHP_INI_USER:
                    $ret = 'scripts';
                    break;
                case self::PHP_INI_PERDIR:
                    $ret = 'php.ini | .htaccess | httpd.conf';
                    break;
                case 6:
                    $ret = 'php.ini | .htaccess | httpd.conf';
                    break;
                case self::PHP_INI_SYSTEM:
                    $ret = 'php.ini | httpd.conf';
                    break;
                case self::PHP_INI_ALL:
                    $ret = 'anywhere';
                    break;
                default:
                    $ret = '-';
            };
            return $ret;
        }
    };
?>