/**
*   This trigger check that the name of the table is unique for the survey
*   @author Alejandro De Gregorio 
*/
trigger CheckUniquePPITableName on PPITable__c (before insert, before update) {
    // Add the new tables to a map with the name as the key
    Map<String, PPITable__c> namesToTables = new Map<String, PPITable__c>();
    for(PPITable__c table : trigger.new){
        // Add tables to the map
        namesToTables.put(table.Name, table);
    }
    
    // Get the tables that match the same name from the database
    List<PPITable__c> savedTables = [SELECT Id, Name FROM PPITable__c WHERE Name IN :namesToTables.keySet()];
    if(!savedTables.isEmpty()){
    
        // If the tables that match have different Ids add an error
        for(PPITable__c savedTable : savedTables){
            PPITable__c newTable = namesToTables.get(savedTable.Name);
            if(savedTable.Id != newTable.Id) {
                newTable.Name.AddError('The table name must be unique for the survey.');
            }
        }
    }
}