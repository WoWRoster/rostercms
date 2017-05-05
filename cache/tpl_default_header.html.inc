<?php global $roster; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $this->_tpldata['.'][0]['ROSTER_PAGE_TITLE']; ?></title>
<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
<link rel="Icon" href="favicon.ico" type="image/x-icon" />
<?php echo $this->_tpldata['.'][0]['ROSTER_HEAD_CSS']; ?>
<?php echo $this->_tpldata['.'][0]['ROSTER_HEAD_JS']; ?>
<?php echo $this->_tpldata['.'][0]['ROSTER_HEAD']; ?>
</head>

<body>

<?php $this->_tpl_include('menu.html');$this->_tpl_include('notice.html'); ?>