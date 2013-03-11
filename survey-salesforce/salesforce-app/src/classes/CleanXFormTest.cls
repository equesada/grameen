/**
*   This is the test class for the CleanXForm trigger
*   @author Alejandro De Gregorio 
*/
@isTest
private class CleanXFormTest {
	
    static testMethod void TestCleanXForm() {
    	
    	// needed objects to test
    	Survey__c survey = new Survey__c(Name = 'TestSurvey');
        insert survey;
        Attachment att = new Attachment(
            Name = XFormCreator.XFORM_ATTACHMENT,
            ParentId = survey.Id,
            body = Blob.valueOf('test')
            );
        
        insert att;
        
        //update with Saving_XForm__c as true shouldn't delete the XForm
        survey.Saving_XForm__c = true;
        update survey;
        List<Attachment> xformAttachmentsWhenSavingXForm = [select Id from Attachment where ParentId = :survey.Id and name = :XFormCreator.XFORM_ATTACHMENT];
        System.assertEquals(1, xformAttachmentsWhenSavingXForm.size());
        
        // and Saving_XForm__c has to be set to false
        survey.Saving_XForm__c = false;
        System.assertEquals(false, survey.Saving_XForm__c);
        
        // update with Saving_XForm__c as false should delete the XForm
        update survey;
        List<Attachment> xformAttachmentsWhenNotSavingXForm = [select Id from Attachment where ParentId = :survey.Id and name = :XFormCreator.XFORM_ATTACHMENT];
        System.assertEquals(0, xformAttachmentsWhenNotSavingXForm.size());
        
        
        
    }
}