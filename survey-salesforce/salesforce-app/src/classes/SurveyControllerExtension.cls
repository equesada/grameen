public with sharing class SurveyControllerExtension {
	public Boolean isPPIEnabled {get;set;}

	public void init(){
		LicenseSettings__c license = C.getLicense();
		isPPIEnabled = license.PPI_Editable__c;
	}
}