@isTest
private class SurveyControllerExtensionTest {
    static testMethod void checkSaveContinue() {
        SurveyControllerExtension extension = new SurveyControllerExtension();
        extension.init();
        system.assertEquals(C.getLicense().PPI_Editable__c, extension.isPPIEnabled);
    }
}