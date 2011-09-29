<?php

class NDK {
   var $shortname;
   var $name;
   var $downloads;
   var $description;
   var $whatsnew;
   var $whatsnew_archive;
   var $dependencies;
   var $support_status;
   var $attribute;
 
   function NDK($shortname) {
     $this->shortname = $shortname;
     $this->name = "";
     $this->description = "";
     $this->whatsnew = "";
     $this->whatsnew_archive = ""; 
     $this->dependencies = "";
     $this->support_status = "";
     $this->attribute = "";
   }

   function getShortName() {
     return $this->shortname;
   }

   function getName() {
     return $this->name;
   }
   
   function getDownloads() {
     return $this->downloads;
   }

   function getDoc() {
     return $this->doc;
   } 

   function getSample() {
     return $this->samplecode;
   }

   function getDescription() {
     return $this->description;
   }

   function getWhatsNew() {
     return $this->whatsnew;
   }

   function getWhatsNewArchive() {
     return $this->whatsnew_archive;
   }

   function getDependencies() {
     return $this->dependencies;
   }

   function getSupportStatus() {
     return $this->support_status;
   }

   function setShortName($shortname) {
     $this->shortname .= $shortname;
   }

   function setName($name) {
     $this->name .= $name;
   }

   function setDownloads($download) {
     $this->downloads[]	= $download;
   }

   function setDoc($doc) {
     $this->doc .= $doc;
   }

   function setSample($sample) {
     $this->samplecode .= $sample;
   }

   function setDescription($description) {
     $this->description .= $description;
   }

   function setWhatsNew($whatsnew) {
     $this->whatsnew .= $whatsnew;
   }

   function setWhatsNewArchive($whatsnew_archive) {
     $this->whatsnew_archive .= $whatsnew_archive;
   }

   function setDependencies($dependencies) {
     $this->dependencies .= $dependencies;
   }

   function setSupportStatus($support_status) {
     $this->support_status .= $support_status;
   }
     
   function destroy() {

   }
}


class DOWNLOAD {
   var $name;
   var $type;
   var $size;
   var $modified;
   var $update;
   var $path;
 
   function DOWNLOAD($name) {
     $this->name = $name;
     $this->type = "";
     $this->size = "";
     $this->modified = "";
     $this->update = "";
   }

   function getName() {
     return $this->name;
   }

   function getType() {
     return $this->type;
   }

   function getSize() {
     return $this->size;
   }

   function getModified() {
     return $this->modified;
   }

   function getUpdate() {
     return $this->update;
   }
   
   function getPath() {
     return $this->path;
   }   
   
   function setName($name) {
     $this->name = $name;
   }

   function setType($type) {
     $this->type = $type;
   }

   function setSize($size) {
     $this->size = $size;
   }

   function setModified($modified) {
     $this->modified = $modified;
   }

   function setUpdate($update) {
     $this->update = $update;
   }
   
   function destroy() {

   }
}
?>
