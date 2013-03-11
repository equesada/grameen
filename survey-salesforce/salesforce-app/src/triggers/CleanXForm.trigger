/**
*   This trigger deletes the XForm for the version unless it is saving an XForm
*   @author Alejandro De Gregorio 
*/
trigger CleanXForm on Survey__c (before update) {
	
	Set<Id> surveysNotSavingXForm = new Set<Id>();
	
	for(Survey__c newSurvey : trigger.new){
		//if not saving xfrom, save the Id to delete the actual xform later
		if(!newSurvey.Saving_XForm__c){
			surveysNotSavingXForm.add(newSurvey.Id);
			newSurvey.XForm_Status__c = 'NONE';
		}
		else{
			newSurvey.Saving_XForm__c = false;
		}
	}
		
	// delete the attachments for versions that are not saving an xform	
    if (!surveysNotSavingXForm.isEmpty())
        delete [SELECT Id FROM Attachment WHERE ParentId IN :surveysNotSavingXForm AND Name = :XFormCreator.XFORM_ATTACHMENT ];
}