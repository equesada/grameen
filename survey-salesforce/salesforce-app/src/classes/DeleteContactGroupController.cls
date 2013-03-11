/**
*   Grameen: Salesforce - Contact Group
*   Controller for the DeleteContactGroup page
*   @author Alejandro De Gregorio 
*/
public with sharing class DeleteContactGroupController {
    
    //---------------------------------------
    // Public properties
    //---------------------------------------
    public ContactGroup__c contactGroup {get; set;}                          // Current contact group
    public List<ContactGroupSurveyAssignment__c> activeSurvey {get; set;}    // List of active survey assignments
    public Integer activeMemberCount {get; set;}                             // Count of active member in the group


    //---------------------------------------
    // Class contructor
    //---------------------------------------
    public DeleteContactGroupController(ApexPages.StandardController controller) {
        this.contactGroup = (ContactGroup__c)controller.getRecord();
        activeSurvey = [select Id, Survey__r.Id, Survey__r.Name, Survey__r.PublishedDate__c, Survey__r.ResponseCount__c from ContactGroupSurveyAssignment__c where ContactGroup__r.Id = :contactGroup.Id and Survey__r.Status__c = 'Published'];
        activeMemberCount = [select Id from ContactGroupMember__c where ContactGroup__r.Id = :contactGroup.Id and Contact__r.mobilesurveys_Status__c = 'Active'].size();
    }
    
    // Delete the current contact group
    public void deleteContactGroup(){
        delete contactGroup;
    }
}