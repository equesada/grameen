public with sharing class SurveyGroupAssignmentController {
	public ContactGroup[] groupsList{get;set;}
	public boolean showDetails{get;set;}
	public string groupIdToDisplay{get;set;}
	public boolean missingId{get;set;}
	public String surveyName{get;set;}
	private string surveyId;
	Set<Id> isSelectedforthisversion;
	public SurveyGroupAssignmentController(){
		missingId = false;
		surveyId = Apexpages.currentPage().getParameters().get('id');
		if(surveyId != null){
			List<Survey__c> currentSurv = [SELECT Name FROM Survey__c WHERE Id=:surveyId];
			surveyName =  currentSurv.size() >0 ? currentSurv[0].Name : '';
			isSelectedforthisversion = new Set<Id>();
			for(ContactGroupSurveyAssignment__c SurveyAssignation :[SELECT ContactGroup__c,Id FROM ContactGroupSurveyAssignment__c WHERE Survey__c =: surveyId]){
				isSelectedforthisversion.add(SurveyAssignation.ContactGroup__c);
			}
			groupsList = new ContactGroup[]{};
			for (ContactGroup__c cG:[SELECT Name,MemberCount__c,Id FROM ContactGroup__c WHERE Status__c ='Active']){
				boolean selected = isSelectedforthisversion.contains(cG.Id);
				groupsList.add(new ContactGroup(cG,selected));
			}
			showDetails = false;
		}else{
			missingId = true;
			ApexPages.Message myMsg = new ApexPages.Message(ApexPages.Severity.FATAL, 'Missing Id parameter.');
			ApexPages.addMessage(myMsg);
		}
	}
	public class ContactGroup{
		public string Name{get{return cGroup.Name;}}
		public string Id{get{return cGroup.Id;}}
		public integer Members{get{return integer.valueof(cGroup.MemberCount__c);}}
		public boolean isSelected{get;set;}
		private ContactGroup__c cGroup;
		public ContactGroup(ContactGroup__c contactGroup,boolean selected){
			cGroup = contactGroup;
			isSelected = selected;
		}
	}
	public void ShowDetails(){
		showDetails = true;
	}
	public void HideDetails(){
		showDetails = false;
	}
	public PageReference Save(){
		ContactGroupSurveyAssignment__c[] groupsAssignmentToSave = new ContactGroupSurveyAssignment__c[]{};
		for(ContactGroup cG:groupsList){
			if(cG.isSelected == true){
				groupsAssignmentToSave.add(new ContactGroupSurveyAssignment__c(ContactGroup__c = cG.Id,Survey__c = surveyId));
			}
		}

		//Delete all the preview Assignments for this survey, then insert all new assignments.
		delete [SELECT Id FROM ContactGroupSurveyAssignment__c WHERE Survey__c =: surveyId];
		insert groupsAssignmentToSave;

		return Page.SurveyManager;
	}
}