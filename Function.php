<?php

/**
 * @param string $dir
 * @return bool|string if success, returns removed dir, faild, returns false.
 * @throws FileNotFoundException if file does not exist.
 */
function removeDir(string $dir){
	if(!file_exists($dir)){
		return false;
	}
	
	if(substr($dir, -1) !== "/"){
		$dir .= "/";
	}
	
	foreach(array_diff(scandir($dir), [".", ".."]) as $files){
		$realFilePath = $dir . $files;
		
		if(file_exists($realFilePath)){
			if(is_file($realFilePath)){
				unlink($realFilePath);
			}elseif(is_dir($realFilePath)){
				$dirs = array_diff(scandir($realFilePath), [".", ".."]);
				if(empty($dirs)){
					rmdir($dirs);
				}else{
					removeDir($dirs);
				}
			}
		}else{
			throw new FileNotFoundException();
		}
	}
	
	$found = array_diff(scandir($dir), [".", ".."]);
	
	if(empty($found)){
		rmdir($dir);
		return $dir;
	}else{
		removeDir($dir);
	}
}
class FileNotFoundException extends Exception{
}

/**
 * @param string $origin
 * @param string $to
 * @param bool   $removeDir
 * @throws FileNotFoundException
 * @throws InvalidFileException
 */
function copyDir(string $origin, string $to, bool $removeDir = false){
	
	if(!file_exists($origin)){
		return false;
	}
	
	if(!file_exists($to)){
		return false;
	}
	
	if(substr($origin, -1) !== "/"){
		$origin .= "/";
	}
	
	if(substr($to, -1) !== "/"){
		$to .= "/";
	}
	
	if(!is_dir($origin) or !is_dir($to)){
		return false;
	}
	
	foreach(array_diff(scandir($origin), [".", ".."]) as $files){
		if(file_exists($origin . $files)){
			$realPath = $origin . $files;
			
			if(is_dir($realPath)){
				mkdir($to . $files);
				copyDir($realPath);
			}elseif(is_file($realPath)){
				copy($realPath, $to);
			}else{
				throw new InvalidFileException();
			}
		}else{
			throw new FileNotFoundException();
		}
	}
	
	if($removeDir){
		removeDir($origin);
	}
}

class InvalidFileException extends Exception{
}
