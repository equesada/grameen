/**
 *Test the proper display of all the Contacts Groups and the proper creation of the contact Assignment Objects.
 */
@isTest
private class SurveyGroupAssignmentControllerTest {

    static testMethod void myUnitTest() {
        ContactGroup__c contactGroup1 = new ContactGroup__c(Name='contactGroupTest1');
        ContactGroup__c contactGroup2 = new ContactGroup__c(Name='contactGroupTest2');
        insert new ContactGroup__c[]{contactGroup1,contactGroup2};
        Contact contact1 = new Contact(LastName='contactTest1');
        Contact contact2 = new Contact(LastName='contactTest2');
        insert new Contact[]{contact1,contact2};
        ContactGroupMember__c groupMember1 = new ContactGroupMember__c(Contact__c = contact1.Id,ContactGroup__c=contactGroup1.Id);
        ContactGroupMember__c groupMember2 = new ContactGroupMember__c(Contact__c = contact2.Id,ContactGroup__c=contactGroup2.Id);
        insert new ContactGroupMember__c[]{groupMember1,groupMember2};
        Survey__c newSurvey = new Survey__c();
        insert newSurvey;

	   	Apexpages.currentPage().getParameters().put('id',newSurvey.Id);
	   	SurveyGroupAssignmentController controller = new SurveyGroupAssignmentController();
	   	//Get all the Active ContactGroups in the ORG
	   	ContactGroup__c[] allContactGroups = [SELECT Id FROM ContactGroup__c WHERE Status__c ='Active'];
	   	controller.ShowDetails();
	   	controller.HideDetails();
	   	//Checks if all the available contacts groups were loaded.
	   	System.assertEquals(allContactGroups.size(), controller.groupsList.size());
	   	//Assign the contactGroup1 to the current survey, check of the record is properly created.
	   	for(integer i=0;i<controller.groupsList.size();i++){
	   		if(controller.groupsList[i].Id == contactGroup1.Id){
	   			controller.groupsList[i].isSelected = true;
	   			break;
	   		}
	   	}
	   	controller.Save();
		ContactGroupSurveyAssignment__c[] SurveyAssignments = [SELECT Id,ContactGroup__c,Survey__c FROM ContactGroupSurveyAssignment__c WHERE  Survey__c=:newSurvey.Id and ContactGroup__c=:contactGroup1.Id];
		//This query should throw 1 record related to this survey and to the contactgroup1
		System.assertEquals(1, SurveyAssignments.size());
		//Re create the controller and check if the contactGroup1 is checked for this survey
		controller = new SurveyGroupAssignmentController();
		for(integer i=0;i<controller.groupsList.size();i++){
	   		if(controller.groupsList[i].Id == contactGroup1.Id){
	   			system.assertEquals(true,controller.groupsList[i].isSelected);
	   			break;
	   		}
	   	}
	   	//Check error Handler if Id parameter is missing.
	   	Apexpages.currentPage().getParameters().remove('id');
	   	controller = new SurveyGroupAssignmentController();
	   	System.assertEquals(1,Apexpages.getMessages().size());
    }
}
