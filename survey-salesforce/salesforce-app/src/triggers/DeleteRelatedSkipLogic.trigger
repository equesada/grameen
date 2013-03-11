trigger DeleteRelatedSkipLogic on Option__c (before delete) {
    delete [SELECT Id FROM SkipCondition__c WHERE Value__c IN :trigger.oldMap.keyset()];
}