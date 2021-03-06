<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/MesClasses/MyUpload.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 27/12/2018
 * Time: 09:08
 */

namespace App\MesClasses;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use ZipArchive;


class MyUpload
{
    private $dir;

    public function __construct(KernelInterface $kernel)
    {
        $this->dir = $kernel->getProjectDir() . '/public/upload/';
    }

    /**
     * @param UploadedFile $fichier
     * @param              $destination
     * @param array        $extensions
     *
     * @return bool|string
     * @throws Exception
     */
    public function upload(UploadedFile $fichier, $destination, $extensions = [])
    {
        $extension = $this->getExtension($fichier);
        $this->dir .= $this->valideDir($destination);

        if ($fichier !== null) {

            if ((count($extensions) > 0) && !in_array($extension, $extensions)) {
                return false; //extension interdite
            }

            $nomfile = random_int(1, 99999) . '_' . date('YmdHis') . '.' . $extension;
            $fichier->move($this->dir, $nomfile);

            return $this->dir . $nomfile;
        }

        return null;
    }

    private function valideDir($dir)
    {
        if ($dir[0] === '/') {
            $dir = substr($dir, 1, strlen($dir));
        }

        if ($dir[strlen($dir) - 1] !== '/') {
            $dir .= '/';
        }

        return $dir;
    }

    /**
     * @param UploadedFile $fichier
     *
     * @return string
     */
    public function getExtension(UploadedFile $fichier): string
    {
        return $fichier->getClientOriginalExtension();
    }

    /**
     * @param $dossierTemp
     * @param $dossierDest
     */
    public function traitePhoto($dossierTemp, $dossierDest): void
    {
        $folder = $this->dir . $dossierTemp;
        $dossier = opendir($folder);
        $newdir = $this->dir . $dossierDest;
        //2)Tant que le dossier est aps vide
        while ($fichier = readdir($dossier)) {

            if ($fichier !== '.' && $fichier !== '..') {
                $t = explode('.', $fichier);
                $vidage = $folder . $fichier;
                if ($t[1] === 'jpg' || $t[1] === 'jpeg') {
                    $f = explode('_', $fichier);
                    $name = $f[count($f) - 1];
                    rename($vidage, $newdir . $name);
                } else {
                    unlink($vidage); //suppression du fichier
                }
            }
        }
        closedir($dossier);
    }

    public function extractZip(bool $fichier, $dest): bool
    {
        chmod($fichier, 0777);
        $filezip = realpath($fichier);

        $zip = new ZipArchive;
        // Ouvrir l'archive
        if ($zip->open($filezip) !== true) {

            return false;
        }
        // Extraire le contenu dans le dossier de destination
        $zip->extractTo($this->dir . $dest);
        // Fermer l'archive
        $zip->close();
        unlink($fichier); //suppression du zip.

        return true;
    }
}
