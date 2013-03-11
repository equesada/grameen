public with sharing class DeviceMassStatusUpdateController {
ApexPages.StandardsetController setDev;

	public DeviceMassStatusUpdateController(ApexPages.StandardsetController controller) {
	setDev = controller;
}

	/**
	* Changes the status of selected Devices to Active.
	*/
	public PageReference activateDevices(){
		return changeStatus('Active');
	}

	/**
	* Changes the status of selected Devices to Inactive.
	*/
	public PageReference deactivateDevices(){
		return changeStatus('Inactive');
	}

	/**
	* This method changes the status of the selected Devices to given parameter.
	*/
	public PageReference changeStatus(String status){
		if(setDev.getSelected() != null && !setDev.getSelected().isEmpty()){
			system.debug('entro al if change status');
			for( integer i = 0; i < setDev.getSelected().size(); i++ ){
				setDev.getSelected().get(i).put('Status__c', status);

			}
			update setDev.getSelected();

		}
		return redirectToList();
	}
	
	/**
	*  Deletes the selected Devices.
	*/
	
	public PageReference deleteDevices(){
		PageReference DeviceListPage = new PageReference(ApexPages.currentPage().getHeaders().get('Referer'));
		DeviceListPage.setRedirect(true);
		if(setDev.getSelected() != null && !setDev.getSelected().isEmpty()){
				delete setDev.getSelected();
		}
		return redirectToList();
	}

	/**
	*  Returns the list pagerefence
	*/
	
	private  PageReference redirectToList(){
		String ObjectPrefix = Device__c.sObjectType.getDescribe().getKeyPrefix();
        PageReference p = new PageReference('/'+ObjectPrefix);
        p.setRedirect(true);
        return p; 
	}
}