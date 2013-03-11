public with sharing class ContactGroupControllerExtension {

    public static final String STATUS_ACTIVE = 'Active';
    public static final String STATUS_INACTIVE = 'Inactive';
    public static final String STATUS_PUBLISHED = 'Published';

    // Current contact group
    public ContactGroup__c contactGroup {get; set;}

    // Lists for selected elements
    public List<Id> selectedMemberList {get; set;}
    public List<Id> selectedNonMemberList {get; set;}
    public List<Id> selectedUnassignedSurveyList {get; set;}
    public List<Id> selectedAssignedSurveyList {get; set;}

    // Cache of objects to add or remove when saving the group
    private Set<Contact> memberToAdd;
    private Set<Contact> memberToRemove;
    private Set<Survey__c> surveyToAdd;
    private Set<Survey__c> surveyToRemove;
    private ApexPages.StandardController stdController;

    // Constructor
    public ContactGroupControllerExtension(ApexPages.StandardController controller){
        this.stdController = controller;
        this.contactGroup = (ContactGroup__c)controller.getRecord();
        this.memberToAdd = new Set<Contact>();
        this.memberToRemove = new Set<Contact>();
        this.surveyToAdd = new Set<Survey__c>();
        this.surveyToRemove = new Set<Survey__c>();
    }

    // Return the options for the non member list with contacts that aren't members
    public List<SelectOption> getNonMemberList(){
        // Get the actual member list for the group
        Set<Id> memberIds = new Set<id>();
        for(ContactGroupMember__c c : [select Contact__c from ContactGroupMember__c where ContactGroup__r.Id = :this.contactGroup.Id]){
            memberIds.add(c.Contact__c);
        }

        // Add the new member to the filter to avoid showing them in the non member list
        for(Contact contactToAdd : this.memberToAdd){
            memberIds.add(contactToAdd.Id);
        }

        // Get the non member contacts and create the options for the non member select
        List<SelectOption> nonMemberOptions = new List<SelectOption>();
        for(Contact contact : [select Id, Name
                               from Contact
                               where mobilesurveys_Status__c = :STATUS_ACTIVE and Id not in :memberIds]) {
            nonMemberOptions.add(new SelectOption(contact.Id, contact.Name));
        }
        // Add options for the removed members for visual consistence
        for(Contact contactToRemove : memberToRemove){
            nonMemberOptions.add(new SelectOption(contactToRemove.Id, contactToRemove.Name));
        }

        return nonMemberOptions;
    }

    // Return the options of the members fo the group
    public List<SelectOption> getMemberList(){
        // Create a set with the id of the contacts to delete to filter the member list
        Set<Id> contactsToRemove = new Set<Id>();
        for(Contact contactToRemove : this.memberToRemove){
            contactsToRemove.add(contactToRemove.Id);
        }

        // Get the actual member list for the group
        List<ContactGroupMember__c> memberList = [select Id, Contact__r.Id, Contact__r.Name
                                                  from ContactGroupMember__c
                                                  where ContactGroup__r.Id = :contactGroup.Id and Contact__c not in :contactsToRemove];

        // Create the options for the member select
        List<SelectOption> memberOptions = new List<SelectOption>();
        for(ContactGroupMember__c cgm : memberList) {
            memberOptions.add(new SelectOption(cgm.Contact__r.Id, cgm.Contact__r.Name));
        }

        // Add the new members to the select for visual consistence
        for(Contact contactToAdd : memberToAdd){
            memberOptions.add(new SelectOption(contactToAdd.Id, contactToAdd.Name));
        }

        return memberOptions;
    }

    // Return the options for the unassigned survey list
    public List<SelectOption> getUnassignedSurveyList(){
        List<ContactGroupSurveyAssignment__c> surveyAssignments = [select Survey__r.Id from ContactGroupSurveyAssignment__c where ContactGroup__r.Id = :contactGroup.Id];

        // Convert the list to a set of ids to use as a filter in the query
        Set<Id> assignmentsIds = new Set<Id>();
        for(ContactGroupSurveyAssignment__c cgsa : surveyAssignments){
            assignmentsIds.add(cgsa.Survey__r.Id);
        }

        // Add the new survey to the filter to avoid showing them in the unassigned survey list
        for(Survey__c survey : surveyToAdd){
            assignmentsIds.add(survey.Id);
        }

        // Get unassigned survey
        List<Survey__c> unassignedSurvey = [select Id, Name
                                             from Survey__c
                                             where Status__c = :STATUS_PUBLISHED and Id not in :assignmentsIds];

        // Get the unassigned surveys and create the options for the unassigned survey select
        List<SelectOption> unassignedSurveyOptions = new List<SelectOption>();
        for(Survey__c survey : unassignedSurvey){
            unassignedSurveyOptions.add(new SelectOption(survey.Id, survey.Name));
        }
        // Add the deleted survey to the list for visual consistence
        for(Survey__c survey : this.surveyToRemove){
            unassignedSurveyOptions.add(new SelectOption(survey.Id, survey.Name));
        }

        return unassignedSurveyOptions;
    }

    public List<SelectOption> getAssignedSurveyList(){
        // Create a set with the id of the surveys to delete to filter the list
        Set<Id> surveyToRemoveIds = new Set<Id>();
        for(Survey__c survey : this.surveyToRemove){
            surveyToRemoveIds.add(survey.Id);
        }

        // Get the actual survey list for the group
        List<ContactGroupSurveyAssignment__c> assignmentsList = [select Survey__r.Id, Survey__r.Name
                                                          from ContactGroupSurveyAssignment__c
                                                          where ContactGroup__r.Id = :contactGroup.Id and Survey__r.Id not in :surveyToRemoveIds];

        // Create the options for the assigned survey select
        List<SelectOption> assignedOptions = new List<SelectOption>();
        for(ContactGroupSurveyAssignment__c assignment : assignmentsList){
            assignedOptions.add(new SelectOption(assignment.Survey__r.Id, assignment.Survey__r.Name));
        }

        // Add the new assigned surveys to the select for visual consistence
        for(Survey__c survey : surveyToAdd){
            assignedOptions.add(new SelectOption(survey.Id, survey.Name));
        }

        return assignedOptions;
    }

    // Return the survey assignments for the group. Used only when group is inactive.
    public List<ContactGroupSurveyAssignment__c> getSurveyAssignments(){
        return [select Id, Survey__r.Name, Survey__r.Alias__c, DatePublishedToThisGroup__c
                from ContactGroupSurveyAssignment__c
                where ContactGroup__r.Id = :contactGroup.Id];
    }

    // Add the selected contacts to the cache of members to add when saving the group
    public void addMember(){
        memberToAdd.addAll([select Id, Name from Contact where Id in :this.selectedNonMemberList]);
        cleanMemberSets();
    }

    // Add the selected contacts to the cache of members to remove when saving the group
    public void removeMember(){
        memberToRemove.addAll([select Id, Name from Contact where Id in :this.selectedMemberList]);
        cleanMemberSets();
    }

    // Add the selected survey to the cache of surveys to add when saving the group
    public void addSurvey(){
        surveyToAdd.addAll([select Id, Name from Survey__c where Id in :this.selectedUnassignedSurveyList]);
        cleanSurveySets();
    }

    // Add the selected surveys to the cache of surveys to remove when saving the group
    public void removeSurvey(){
        surveyToRemove.addAll([select Id, Name from Survey__c where Id in :this.selectedAssignedSurveyList]);
        cleanSurveySets();
    }

    public PageReference saveGroup(){
        // Manage validation
        try{
            // Save changes in the contact group information
            upsert contactGroup;
        }
        catch(DmlException e){
            ApexPages.Message auxMessage = new ApexPages.Message(ApexPages.Severity.ERROR, e.getMessage());
            ApexPages.addMessage(auxMessage);
            return null;
        }

        // Create association objects for new member and save them
        List<ContactGroupMember__c> newCGMembers = new List <ContactGroupMember__c>();
        for(Contact contact : this.memberToAdd){
            newCGMembers.add(new ContactGroupMember__c(Contact__c = contact.Id, ContactGroup__c = this.contactGroup.Id));
        }
        upsert newCGMembers;

        // Delete the association objects of the removed member
        delete [select Id from ContactGroupMember__c where Contact__c in :memberToRemove];

        // Survey assignment can be changed only in active groups
        if(contactGroup.Status__c != STATUS_INACTIVE){
            // Create the association object for the assigned surveys and save them
            List<ContactGroupSurveyAssignment__c> newCGSurveyAssignments = new List<ContactGroupSurveyAssignment__c>();
            for(Survey__c survey : this.surveyToAdd){
                newCGSurveyAssignments.add(new ContactGroupSurveyAssignment__c(Survey__c = survey.Id, ContactGroup__c = this.contactGroup.Id));
            }
            upsert newCGSurveyAssignments;

            // Delete the association object for the unassigned surveys
            delete [select Id from ContactGroupSurveyAssignment__c where Survey__c in :surveyToRemove];
        }

        // Redirect to the group detail view
        return new PageReference('/' + contactGroup.Id);
    }

    public PageReference saveAndNew(){
        // Save as usual and redirect to blank edit
        PageReference resultPage = saveGroup();
        if (resultPage == null){
            return resultPage;
        }else {


            // The URL for the new record page is the {org URL}/{3 letter object prefix}/e?"
            string reference = '/' + ('' + contactGroup.get('Id')).subString(0, 3) + '/e?';
            return new PageReference(reference);
        }
    }
    
    // Return the page reference to redirect on cancel
    public PageReference cancel(){
        return this.contactGroup.Id != null
            ? stdController.cancel()
            : Page.ContactGroupList;
    }

    // Remove the member added and removed
    private void cleanMemberSets(){
        for(Contact contact : this.memberToRemove){
            if(this.memberToAdd.remove(contact)){
                this.memberToRemove.remove(contact);
            }
        }
    }

    // Remove the survey added and removed
    private void cleanSurveySets(){
        for(Survey__c survey : this.surveyToRemove){
            if(this.surveyToAdd.remove(survey)){
                this.surveyToRemove.remove(survey);
            }
        }
    }
}
