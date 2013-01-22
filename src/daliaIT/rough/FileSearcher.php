<?php
namespace daliaIT\rough;
class FileSearcher
{         
    public function search($data){
        $results = glob($data);
        if(!$results) return array();
        foreach(array('.','..') as $removeThis){
            $key = array_search($removeThis, $results);
            if($key !== false){
                unset($results[$key]);
            }
        }

        if(!$results) return array();
        return array_unique($results);
    }
    
    public function searchRecursive($filePattern, $base=''){
        $files = array();
        foreach($this->search($base.'/'.$filePattern) as $file){
            if(!is_dir($file)) $files[] = $file;
        }
        foreach($this->search($base.'/*') as $dir){
            if(is_dir($dir)){
                $results =$this->searchRecursive($filePattern, $dir);
                $files = array_merge($files,$results);
            }
        }
        return $files;
    }
}