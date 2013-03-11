/**
*   Grameen: Salesforce - PPI Upload
*   Controller for the LookupTables page
*   @author Alejandro De Gregorio
*/
public with sharing class LookupTablesController {

    //---------------------------------------
    // Page properties
    //---------------------------------------
    private Id surveyId;                                // The id of the survey
    public Survey__c theSurvey { get; set; }            // The actual survey
    public List<TableWrapper> ppiTables { get; set; }   // A list with all tables for the survey
    public PPITable__c selectedTable { get; set; }      // The selected table
    public String selectedTableId { get;                // The id of the selected table
        set {
            if(value == ''){
                selectedTable = null;
            }
            else {
                selectedTable = [SELECT Id, Name 
                                 FROM PPITable__c 
                                 WHERE Id = :value];
                selectedTableId = value;
            }
        }
    }
    
    
    //---------------------------------------
    // File upload properties
    //---------------------------------------
    public string fileName{get;set;}                    // The name of the selected file
    public string fileContent{get;set;}                 // The string content of the file
    public Blob fileBinary{get;set;}                    // The binary content of the file
    private List<PPITable__c> createdTables;            // Tables created in the current upload
    
    
    //---------------------------------------
    // Class constructor
    //---------------------------------------
    public LookupTablesController(){
        this.surveyId = ApexPages.currentPage().getParameters().get('id');
        this.theSurvey = [SELECT Id, Name, Alias__c, IsPPI__c 
                          FROM Survey__c 
                          WHERE Id =: this.surveyId];
        this.selectedTableId = '';
        refreshTableList();
    }
    
    //-------------------------------------------------------        
    //URL License Control, can't execute code without licence permission
    public PageReference checkLicense(){
       LicenseSettings__c license = C.getLicense();
       if( !license.PPI_Editable__c && theSurvey.IsPPI__c )
            throw new C.LicenseException(system.label.SYSTEM_ERR_NOLICENSEPERMISSION);
       return null; 
    }
    //--------------------------------------------------------
    
    // Refresh the table list to show in the page
    private void refreshTableList(){
        this.ppiTables = new List<TableWrapper>();
        
        // Get the data sets for the survey
        List<PPITableDataSet__c> tableDataSets = [SELECT Id, PPITable__r.Name, PPITable__r.Id 
                                    FROM PPITableDataSet__c 
                                    WHERE Survey__c = :this.surveyId
                                    ORDER BY Id ASC];

        // Get all lines for the data sets
        List<PPITableLine__c> allLines = [SELECT Id, PPITableDataSet__r.Id, From__c, To__c, Probability__c 
                                          FROM PPITableLine__c 
                                          WHERE PPITableDataSet__c IN :tableDataSets 
                                          ORDER BY PPITableDataSet__r.Id ASC, From__c];
        
        // Take the lines for each data set and create a wrapper instance
        Integer actualLine = 0;
        for(PPITableDataSet__c dataSet : tableDataSets){
            List<PPITableLine__c> lines = new List<PPITableLine__c>();
            // Iterate in the list of lines ordered by data set Id and move each one to a temp list
            for(Integer i=0; i<allLines.size(); ){
                PPITableLine__c line = allLines[0];
                if(line.PPITableDataSet__r.Id == dataSet.Id){
                    lines.add(line);
                    allLines.remove(0);
                }
                else{
                    break;
                }
            }
            this.ppiTables.add(new TableWrapper(dataSet, lines));
        }
    }
    
    // Get a map with a data set for each table
    private Map<String, PPITableDataSet__c> getDataSetMap(Set<String> tableNames){
        Map<String, PPITableDataSet__c> dataSetMap = new Map<String, PPITableDataSet__c>();
        List<PPITableDataSet__c> newDataSets = new List<PPITableDataSet__c>();
        List<PPITable__c> newTables = new List<PPITable__c>();

        // Get the old data sets that match with the table names
        for(PPITableDataSet__c dataSet : [SELECT Id, PPITable__r.Id, PPITable__r.Name 
                                          FROM PPITableDataSet__c WHERE Survey__r.Id = :this.surveyId AND PPITable__r.Name IN :tableNames]){
                dataSetMap.put(dataSet.PPITable__r.Name, dataSet);
                tableNames.remove(dataSet.PPITable__r.Name);
        }
        
        // Create a new data set for each table that already exists in the database
        for(PPITable__c table : [SELECT Id, Name FROM PPITable__c WHERE Name in :tableNames]){
            PPITableDataSet__c newDataSet = new PPITableDataSet__c(PPITable__c = table.Id, Survey__c = this.surveyId);
            dataSetMap.put(table.Name, newDataSet);
            tableNames.remove(table.Name);
        }
        
        // Create new tables that don't exists in the database
        for(String name : tableNames){
            PPITable__c newTable = new PPITable__c(Name = name);
            newTables.add(newTable);
        }
        insert newTables;
        this.createdTables = newTables;

        // Create data sets for the new tables
        for(PPITable__c table : newTables){
            PPITableDataSet__c newDataSet = new PPITableDataSet__c(PPITable__c = table.Id, Survey__c = this.surveyId);
            dataSetMap.put(table.Name, newDataSet);
        }
        
        upsert dataSetMap.values();
        
        return dataSetMap;
    }

    // Remove the created tables and data sets. Used when an error occurs in the upload
    private void cleanCreatedTables(Map<String, PPITableDataSet__c> dataSets){
        delete dataSets.values();
        delete this.createdTables;
    }

    // Reset the upload variables
    private void clearVariables(){
        this.createdTables = null;
        this.fileName = null;
        this.fileContent = null;
        this.fileBinary = null;
    }
    
    // Add the specified score range to the given set
    // If there is an error returns the error message, otherwise returns null
    private String addScoreRange(Decimal rangeFrom, Decimal rangeTo, Set<Decimal> coveredRange){
        // If the range definition ir wrong return null
        if(rangeFrom > rangeTo){
            return 'The range from ' + rangeFrom + ' to ' + rangeTo + 
                   ' is wrong. Fix the error and try again.';
        }
    
        // Create a set for the new range
        Set<Decimal> newRange = new Set<Decimal>();
        for(Decimal i = rangeFrom; i <= rangeTo; i++){
            newRange.add(i);
        }
        
        // If there are any intersection between the sets return null
        if(coveredRange.removeAll(newRange)){
            return 'The range from ' + rangeFrom + ' to ' + rangeTo + 
                   ' is intersected with a previous one. Fix the error and try again.';
        }
        else {
            // Add the new range and return the complete covered range
            coveredRange.addAll(newRange);
            return null;
        }
    }
    
    
    //---------------------------------------
    // Actions
    //---------------------------------------
    
    // Read and parse the uploaded file
    public Pagereference readFile()
    {
        // Avoid process if there isn't a selected file
        if(this.fileBinary == null){
            ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR,
                                                               'Please select a file to upload.');
            ApexPages.addMessage(errormsg);
            clearVariables();
            return null;
        }

        // Create the PPI table and line lists
        Map<String, PPITableDataSet__c> stringToDataSets = new Map<String, PPITableDataSet__c>();
        List<PPITableLine__c> uploadedLines = new List<PPITableLine__c>();
        
        // Convert the binary content to string
        // If it fails, show an error and don't insert the table
        try{
            fileContent = fileBinary.toString();
        } catch(StringException e){
            ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR,
                                                               'The file is not in UTF-8 format and contains special characters. ' +
                                                               'It must be converted to be able upload it.');
            ApexPages.addMessage(errormsg);
            clearVariables();
            return null;
        }
        
        // Parse the file content
        String[] fileLines = fileContent.split('\n');
        Integer columnCount = fileLines[0].split(',').size();
        Set<Decimal> coveredRange = new Set<Decimal>();
        List<String> tableNames = new List<String>();

        // Validate column count and create table data sets
        if(columnCount >= 3){
            tableNames = fileLines[0].split(',');
            tableNames.remove(1);
            tableNames.remove(0);
            Set<String> namesSet = new Set<String>();
            namesSet.addAll(tableNames);

            // Validate repeated column names
            if(namesSet.size() != tableNames.size()){
                ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR,
                                                                   'There is an error in the file. ' +
                                                                   'Some table names are repeated. ' + 
                                                                   'Fix the error and try again.');
                ApexPages.addMessage(errormsg);
                clearVariables();
                return null;
            }
            
            // Validate tables name length
            Integer tableNameLength = Schema.SObjectType.PPITable__c.fields.Name.getLength();
            for(String name : tableNames){
                if(name.length() > tableNameLength){
                    ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR,
                                                                       'There is an error in the file. ' +
                                                                       '\'' + name + '\' is too long for a table name.' +
                                                                       'The max length is ' + tableNameLength + '.' +
                                                                       'Fix the error and try again.');
                    ApexPages.addMessage(errormsg);
                    clearVariables();
                    return null;
                }
            }

            stringToDataSets = getDataSetMap(namesSet);
        }
        else {
            ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR,
                                                               'There is an error in the file. ' +
                                                               'Wrong column count at the first line. ' + 
                                                               'Fix the error and try again.');
            ApexPages.addMessage(errormsg);
            clearVariables();
            return null;
        }

        // Remove first line that was already precessed
        fileLines.remove(0);

        // Parse each line
        Integer lineIndex = 1;
        for (String line : fileLines){
            String[] inputValues = line.split(',');
            List<Decimal> numValues = new List<Decimal>();

            // Validate column count
            if(inputValues.size() != columnCount){
                ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR, 
                                                                   'There is an error in the file. ' +
                                                                   'Wrong column count at line ' + (lineIndex+1) + '. ' + 
                                                                   'Fix the error and try again.');
                ApexPages.addMessage(errormsg);

                // Remove the inserted objects and clean the variables
                cleanCreatedTables(stringToDataSets);
                clearVariables();
                return null;
            }
                
            // Convert the numbers
            Integer column = 1;
            for(String strValue : inputValues){
                try{
                    numValues.add(Decimal.valueOf(strValue));
                }
                catch (Exception e){
                    ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR, 
                                                                       'There is an error in the file. ' +
                                                                       'The value at column ' + column + ' of line ' + (lineIndex+1) + ' can\'t be converted to integer. ' + 
                                                                       'Fix the error and try again.');
                    ApexPages.addMessage(errormsg);

                    // Remove the inserted objects and clean the variables
                    cleanCreatedTables(stringToDataSets);
                    clearVariables();
                    return null;
                }
                column++;
            }

            // Check that the range is valid
            String error = addScoreRange(numValues[0], numValues[1], coveredRange);
            if(error != null){
                ApexPages.Message errormsg = new ApexPages.Message(ApexPages.severity.ERROR, 
                                                                   'There is an error at line ' + (lineIndex+1) + '. ' + error);
                ApexPages.addMessage(errormsg);

                // Remove the inserted objects and clean the variables
                cleanCreatedTables(stringToDataSets);
                clearVariables();
                return null;
            }
            
            // Create the lines for each table
            for(Integer j = 2; j < columnCount; j++){
                PPITableLine__c tableLine = new PPITableLine__c(PPITableDataSet__c = stringToDataSets.get(tableNames[j-2]).Id,
                                                           From__c = numValues[0], To__c = numValues[1],
                                                           Probability__c = numValues[j]);
                uploadedLines.add(tableLine);
            }

            lineIndex++;
        }
        
        // Clean old lines in existing data sets
        delete [SELECT Id FROM PPITableLine__c WHERE PPITableDataSet__c IN :stringToDataSets.values()];
        
        // Save the lines in the database
        insert uploadedLines;
        
        refreshTableList();
        
        // Clear variables
        clearVariables();
        return null;
    }
    
    // Delete the data set for the specified table
    public void deleteDataSet(){
        delete [SELECT Id FROM PPITableDataSet__c WHERE PPITable__c = :selectedTable.Id AND Survey__c = :this.surveyId];
        refreshTableList();
    }


    //---------------------------------------
    // Inner class definition
    //---------------------------------------
    
    // Wrapper class for the table and lines
    public class TableWrapper {
        public PPITableDataSet__c dataSet { get; set; }
        public List<PPITableLine__c> lines { get; set; }
        
        public TableWrapper(PPITableDataSet__c aDataSet, List<PPITableLine__c> lineList){
            this.dataSet = aDataSet;
            this.lines = lineList;
        }
    }
}