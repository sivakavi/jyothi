<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;

class BaseControllers extends Controller
{
    protected function fileUpload($file, $fileVarName)
    {
        $destinationPath = public_path(). '/uploads/';
        $fileExtenstion = \File::extension($file[$fileVarName]->getClientOriginalName());
        $filename = strtotime("now").".".$fileExtenstion;
        $file[$fileVarName]->move($destinationPath, $filename);
        return $filename;
    }

    protected function fileUploadWithName($file, $fileVarName, $name)
    {
        $destinationPath = public_path(). '/uploads/';
        $fileExtenstion = \File::extension($file[$fileVarName]->getClientOriginalName());
        $filename = $name.'_'.strtotime("now").".".$fileExtenstion;
        $file[$fileVarName]->move($destinationPath, $filename);
        return $filename;
    }
}