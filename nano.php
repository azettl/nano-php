<?php
final class nano{

  private $_sTemplate  = '';
  private $_aData      = null;
  private $_bShowEmpty = false;

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
          list($sFormattedKey, $mParam) = $this->getFunctionNameAndParameter($sKey);
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

  private function getFunctionNameAndParameter($sKey) : array
  {
    // Get method parameter, till now only one is supported
    preg_match_all("/\((.*?)\)/", $sKey, $aParam);

    $mParam = null;
    if($aParam && count($aParam[0]) >= 1){
      $mParam = $this->formatFunctionParameterValue($aParam[1][0]);
    }

    return [
      str_replace($aParam[0][0], '', $sKey), $mParam
    ];
  }

  private function formatFunctionParameterValue($mValue) : string
  {
    if(strpos($mValue, '"') === 0 || strpos($mValue, '\'') === 0) {
      $mValue = substr($mValue, 1);
    }

    if(strpos($mValue, '"') === strlen($mValue)-1 || strpos($mValue, '\'') === strlen($mValue)-1) {
      $mValue = substr($mValue, 0, strlen($mValue)-1);
    }

    return $mValue;
  }
}