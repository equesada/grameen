/**
*   Grameen: Salesforce - Contact Group Manager
*   Controller for the ContactGroupList page
*   @author Alejandro De Gregorio
*/
public with sharing class ContactGroupListController {

    //---------------------------------------
    // Constants
    //---------------------------------------

    public static final String FILTER_ALL = 'All';
    public static final String FILTER_ACTIVE = 'Active';
    public static final String FILTER_INACTIVE = 'Inactive';
    public static final Integer MAX_ROWS_PER_PAGE = 20;


    //---------------------------------------
    // Public properties
    //---------------------------------------

    // Contact group properties
    public ContactGroup__c contactGroup {get; set;}              // Current contact group
    public String currentGroupId {get; set{                      // Id of the current contact group
        currentGroupId = value;
        // Update the current contact group property
        this.contactGroup = [select Id from ContactGroup__c where Id = :currentGroupId];
    }}
    public List<ContactGroup__c> groupList {get{                 // Groups to show in the table of the page
        return (List<ContactGroup__c>) paginator.getRecords();
    } set;}

    public String selectedGroupIds {get; set;}                   // String with the ids of the selected groups (comma separated)

    // Flags
    public boolean displayConfirmationPopUp {get; set;}          // Indicate whether the confirmation popup should be displayed or not
    public boolean targetIsSelection {get; set;}                 // Indicate whether the target of the action is a selection or not

    // Filter properties
    public List<SelectOption> filterList {get; set;}             // Options for the filter select list
    public String filter {get; set{                              // The actual filter value
        if (value == null || value.trim().equals('')
                || String.escapeSingleQuotes(value).contains('\\')){
            filter = FILTER_ALL;
        }else{
            filter = value;
        }
    }}

    private String previousSortField;                             // Indicate the previous sort column
                                                                  // ^(used to check if have to change the order on the same column)
    public String tableOrderColumn {get; set{                     // Indicate the column to sort the records (should be a field name)
        if (value == null || value.trim().equals('')
                || String.escapeSingleQuotes(value).contains('\\') ){
            tableOrderColumn = 'LastModifiedDate';
        }else{
            tableOrderColumn = value.trim() ;
        }
        // Set the previous field
        previousSortField = tableOrderColumn;
    }}

    private String sortOrder;                                      // Indicate the order for the rows

    // Pagination property
    public ApexPages.StandardSetController paginator{get;set;}


    //---------------------------------------
    // Class contructor
    //---------------------------------------
    public ContactGroupListController(ApexPages.StandardController controller) {
        this.targetIsSelection = false;
        this.displayConfirmationPopUp = false;
        this.tableOrderColumn = 'LastModifiedDate';
        this.sortOrder = 'desc';
        this.contactGroup = (ContactGroup__c)controller.getRecord();
        this.filter = FILTER_ALL;
        this.filterList = new List<SelectOption>{new SelectOption(FILTER_ALL, 'All'),
                                                   new SelectOption(FILTER_ACTIVE, 'Active Groups'),
                                                   new SelectOption(FILTER_INACTIVE, 'Inactive Groups')};
        this.groupList = new List<ContactGroup__c>();
        refreshGroupList();
    }

    // Return the active surveys for the current or selected active groups
    public List<ContactGroupSurveyAssignment__c> getActiveSurvey(){
        if(this.targetIsSelection){
            String[] groupIds = this.selectedGroupIds.split(',');
            return [select Id, Survey__r.Id, Survey__r.Name, Survey__r.PublishedDate__c, Survey__r.ResponseCount__c
                    from ContactGroupSurveyAssignment__c
                    where ContactGroup__c in :groupIds and Survey__r.Status__c = 'Published' and ContactGroup__r.Status__c='Active'];
        }
        else{
            return [select Id, Survey__r.Id, Survey__r.Name, Survey__r.PublishedDate__c, Survey__r.ResponseCount__c
                    from ContactGroupSurveyAssignment__c
                    where ContactGroup__r.Id = :contactGroup.Id and Survey__r.Status__c = 'Published' and ContactGroup__r.Status__c='Active'];
        }
    }


    //---------------------------------------
    // PopUp methods
    //---------------------------------------

    public void showPopUp(){
        displayConfirmationPopUp = true;
    }

    public void closeGroupPopup(){
        displayConfirmationPopUp = false;
    }


    //---------------------------------------
    // Action methods
    //---------------------------------------

    // Activate the selected contact groups
    public void activateGroup(){
        if(this.targetIsSelection){
            String[] groupIds = this.selectedGroupIds.split(',');
            List<ContactGroup__c> groupList = [select Id from ContactGroup__c where Id in :groupIds];
            for(ContactGroup__c cGroup : groupList){
                cGroup.Status__c = FILTER_ACTIVE;
            }
            update groupList;
        }
        else{
            this.contactGroup.Status__c = FILTER_ACTIVE;
            update contactGroup;
        }
        refreshGroupList();
    }

    // Inactivate the selected contact groups
    public void inactivateGroup(){
            // Inactivate selection
            if(this.targetIsSelection){
                String[] groupIds = this.selectedGroupIds.split(',');
                List<ContactGroup__c> selectedGroupList = [select Id from ContactGroup__c where Id in :groupIds];
                for(ContactGroup__c cGroup : selectedGroupList){
                    cGroup.Status__c = FILTER_INACTIVE;
                }
                update selectedGroupList;
            }
            // Inactivate current contact group
            else{
                this.contactGroup.Status__c = FILTER_INACTIVE;
                update contactGroup;
            }
        this.displayConfirmationPopUp = false;
        refreshGroupList();
    }

    // Set the order and refresh the list
    public void doSort(){
        this.sortOrder = (this.previousSortField == this.tableOrderColumn && this.sortOrder == 'desc') ? 'asc' : 'desc';
        refreshGroupList();
    }
    
    // Apply the filter to the list
    public void doFilter(){
        // Any time a filter is applyed must show the first page
        this.paginator.setPageNumber(1);
        refreshGroupList();
    }

    // Refresh the contact group list
    public void refreshGroupList(){
        // Basic query
        String queryString = 'select Id, Status__c, Name, MemberCount__c, SurveyCount__c, ' +
                                    'LastModifiedDate, CreatedDate, CreatedById from ContactGroup__c';

        // Add filter to the query
        if(this.filter != 'All')
                queryString = queryString + ' where Status__c = \'' + this.filter + '\'';

        // Set the order
        queryString = queryString + ' order by ' + this.tableOrderColumn + ' ' + this.sortOrder;

        // Setup the pagination controller
        Integer oldPageNumber = 1;
        if (this.paginator != null) oldPageNumber = this.paginator.getPageNumber();
        this.paginator = new ApexPages.StandardSetController(Database.getQueryLocator(queryString));
        this.paginator.setPageSize(MAX_ROWS_PER_PAGE);
        this.paginator.setPageNumber(oldPageNumber);
    }

}
