<?php

namespace com\azettl\nano;

/**
 * The php-nano-template class replaces placeholders in a string with values from an array.
 *
 * @package  php-nano-template
 * @author   Andreas Zettl <info@azettl.net>
 * @see      https://github.com/azettl/php-nano-template
 */
final class nano{

  private $_sTemplate  = '';
  private $_aData      = [];
  private $_bShowEmpty = false;


  /**
   * This method is automatically called by creating a nano object,
   * here you can already pass all needed settings or you use the 
   * setter methods later.
   *
   * @param string $sTemplate    the template as string
   * @param array  $aData        the array containing the data
   * @param bool   $bShowEmpty   true / false
   */
  public function __construct(string $sTemplate = '', array $aData = [], bool $bShowEmpty = false)
  {
    if($sTemplate){
      $this->setTemplate($sTemplate);
    }

    if($aData){
      $this->setData($aData);
    }

    if($bShowEmpty){
      $this->setShowEmpty($bShowEmpty);
    }
  }


  /**
   * This method is automatically called on destruct of an 
   * nano class object.
   */
  public function __destruct()
  {
    $this->_sTemplate  = '';
    $this->_aData      = [];
    $this->_bShowEmpty = false;
  }


  /**
   * This method is used to set the template string in which 
   * the placeholders should get replaced.
   *
   * @param string $sTemplate the template as string
   * @return void
   */
  public function setTemplate(string $sTemplate) : void 
  {
    $this->_sTemplate = $sTemplate;
  }


  /**
   * This method is used to set the template from a relative
   * path.
   *
   * @param string $sRelativePathToFile  the relative path to the template
   * @throws Exception                   if the file is not found
   * @return void
   */
  public function setTemplateFile(string $sRelativePathToFile) : void
  {
    if(!is_file($sRelativePathToFile)) {
      throw new \Exception('Template file not found.');
    }
    
    $sFileContent = file_get_contents($sRelativePathToFile);
    $this->setTemplate($sFileContent);
  }


  /**
   * This method is used to return the current template string.
   *
   * @return string
   */
  public function getTemplate() : string 
  {
    return $this->_sTemplate;
  }


  /**
   * This method is used to set the data array in which 
   * the data for the placeholders is stored.
   *
   * @param array $aData the array containing the data
   * @return void
   */
  public function setData(array $aData) : void 
  {
    $this->_aData = $aData;
  }


  /**
   * This method is used to get the current data array.
   *
   * @return array
   */
  public function getData() : array 
  {
    return $this->_aData;
  }


  /**
   * This method is used to set whether placeholders which could not
   * be replaced shall remain in the output string or not.
   *
   * @param bool $bShowEmpty true / false
   * @return void
   */
  public function setShowEmpty(bool $bShowEmpty) : void 
  {
    $this->_bShowEmpty = $bShowEmpty;
  }


  /**
   * This method is used to get the current show empty placeholder
   * status.
   *
   * @return bool
   */
  public function hasShowEmpty() : bool 
  {
    return $this->_bShowEmpty;
  }


  /**
   * This method replaces the placeholders in the template string with
   * the values from the data object and returns the new string.
   *
   * @return string   the string with the replaced placeholders
   */
  public function render() : string 
  {
    $sOutput = preg_replace_callback(
      '/{(.*?)}/',
      function ($aResult){
        $aToSearch = explode('.', $aResult[1]);
        $aSearchIn = $this->getData();

        foreach ($aToSearch as $sKey) {
          list(
            $sFormattedKey, 
            $mParam
          )       = self::getFunctionNameAndParameter($sKey);
          $mValue = $aSearchIn[$sFormattedKey];

          if(is_string($mValue)) {

            return $mValue;
          } else if(is_object($mValue)) {
            if($mParam){

              return $mValue($mParam);
            }

            return $mValue();
          }
          
          $aSearchIn = $mValue;
        }

        return (
          $this->hasShowEmpty() 
          ? 
          $aResult[0] 
          : 
          ''
        );
      },
      $this->getTemplate()
    );
    
    return preg_replace('/^\s+|\n|\r|\t/m', '', $sOutput);
  }


  /**
   * This method parses the passed key by checking if it is a method name
   * or not and if it is so also retrieving the first parameter (only one 
   * supported right now).
   *
   * @param string $sKey   the key which needs to be replaced by the array value
   * @return array
   */
  private static function getFunctionNameAndParameter($sKey) : array
  {
    preg_match_all("/\((.*?)\)/", $sKey, $aParam);

    return [
      str_replace($aParam[0][0], '', $sKey), 
      preg_replace('/^["\'](.*)["\']$/m', '$1', $aParam[1][0]) // remove quotes
    ];
  }
}