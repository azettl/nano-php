<?php
/**
 * The php-nano-template class replaces placeholders in a string with values from an array.
 *
 * @package  nano
 * @author   Andreas Zettl <info@azettl.net>
 * @see      https://github.com/azettl/php-nano-template
 */
final class nano{

  private $_sTemplate  = '';
  private $_aData      = null;
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
    $this->_aData      = null;
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
   * This method replaces the placeholders in the template string with
   * the values from the data object and returns the new string.
   *
   * @return string   the string with the replaced placeholders
   */
  public function render() : string 
  {
    return preg_replace_callback(
      '/{(.*?)}/',
      function ($aResult){
        $aToSearch  = explode('.', $aResult[1]);
        $aSearchIn  = $this->_aData;

        foreach ($aToSearch as $sKey) {
          list(
            $sFormattedKey, 
            $mParam
          )       = $this->getFunctionNameAndParameter($sKey);
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
          $this->_bShowEmpty 
          ? 
          $aResult[0] 
          : 
          ''
        );
      },
      $this->_sTemplate
    );
  }


  /**
   * This method parses the passed key by checking if it is a method name
   * or not and if it is so also retrieving the first parameter (only one 
   * supported right now).
   *
   * @param string $sKey   the key which needs to be replaced by the array value
   * @return array
   */
  private function getFunctionNameAndParameter($sKey) : array
  {
    preg_match_all("/\((.*?)\)/", $sKey, $aParam);

    return [
      str_replace($aParam[0][0], '', $sKey), 
      $this->formatFunctionParameterValue($aParam[1][0])
    ];
  }


  /**
   * This method removes single and double quotes at the beginning and
   * end of the passed string. 
   *
   * @param string $mValue   the value which needs to get formatted
   * @return mixed           string if a parameter is available, null if not
   */
  private function formatFunctionParameterValue($mValue)
  {
    preg_match_all('/^(["\'])/', $mValue, $aParam);
    
    if($aParam && count($aParam[0]) >= 1){
      $mValue = substr($mValue, 1, strlen($mValue)-2);
    }

    return $mValue;
  }
}