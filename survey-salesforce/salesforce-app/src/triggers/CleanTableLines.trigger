trigger CleanTableLines on PPITableDataSet__c (before delete) {

        delete [SELECT Id FROM PPITableLine__c WHERE PPITableDataSet__c IN :trigger.old];
}