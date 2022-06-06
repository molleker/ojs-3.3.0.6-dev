<?php
$params = array(
    'dbname'   => 'cochrane',
    'username' => 'root',
    'password' => '5jfWkt2Erv',
    'host'     => 'mysql.baze.cochrane.ru',
    'port'     => '65379'
);
 
$dir = __DIR__.'/mysqldumps';
$filename = $dir.'/dump.sql';
$cmd = '/usr/local/mysql51/bin/mysqldump --extended-insert=FALSE --dump-date=FALSE -h'.$params['host'].' -P'.$params['port'].' -u'.$params['username'].' -p'.$params['password'].' '.$params['dbname'].' > '.$filename;
system($cmd);


$git = '/usr/local/bin/git';
$cmd = array();
$cmd[] = 'cd '.$dir;
$cmd[] = $git.' add .';
$cmd[] = $git.' commit -m "Dump '.date('Y-m-d').'"';
$icmd = implode(' && ', $cmd);
print $icmd;
system($icmd);

?>