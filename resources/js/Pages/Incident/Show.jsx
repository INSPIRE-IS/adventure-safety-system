import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head,router} from "@inertiajs/react";
import EditButton from '@/Components/EditButton';
import ManageModal from '@/Components/ManageModal';




const IncidentsIndex = ({auth, incident, can, users,incident_id,category_id }) => {
const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
let datepart1 = "";
let datepart2 = "";
let datepart3 = "";
console.log('incident',incident);

if(incident && incident.incident_date){
    datepart1 = incident?.incident_date?.split(" ")[0];
    datepart2 = incident?.incident_date?.split(" ")[1];
    datepart3 = datepart1?.split("-");
    console.log('datepart3',datepart3[0],datepart3[1],datepart3[2]);
}



const handleOpenManageModal = (mode) => {

    if(incident_id == null || category_id == null){
        alert("Please select at least one incident");
        return;
    }
    setModalMode(mode);
    setCheckedState({incident_id,category_id});
    setIsManageModalOpen(true);
};

const handleCloseModal = () => {
    setIsManageModalOpen(false);
};



    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Incidents" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <EditButton title='Back to Incident Management' width="w-[254px]" height="h-[52px]" icon="./images/back-icon.svg"  onClick={() => router.get(route('incidents.index'))} />
                </div>
                <header className="mb-6 flex items-center justify-between w-[100%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">{incident?.type_incident?.name} {datepart3?.[1]}/{datepart3?.[2]}</h1>                    
                    </div>
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        {can.incident_edit_reminder && (
                        <EditButton title='Send Reminder' width="w-[214px]" height="h-[52px]" icon="./images/add-alert-clock-icon.svg" onClick={() => handleOpenManageModal("reminder")} />
                        )}
                        {can.incident_edit_inspector && (
                        <EditButton title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon="./images/add-user-icon-2.svg" onClick={() => handleOpenManageModal("assign")} />
                        )}
                        {can.incident_edit_date && (
                        <EditButton title='Update Incident Date' width="w-[254px]" height="h-[52px]" icon="./images/clock-setting-gear-icon.svg" onClick={() => handleOpenManageModal("change")} />
                        )}
                    </div>
                </header>

                {/* Incidents Table */}
                <div className=" pl-[40px] pr-[32px] py-5 rounded-3xl bg-white">
                    <div className='flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6'>
                        <div className="pt-[40px]  pb-[32px] self-start ">               
                                <p className="label-header-4 mb-2">Type of Incident</p>
                                <p className="label-sub-header-4 mb-4">{incident?.type_incident?.name}</p>

                                <p className="label-header-4 mb-2">Date of Incident</p>
                                <p className="label-sub-header-4 mb-4">{incident?.incident_date?.split(" ")[0]}</p>

                                <p className="label-header-4 mb-2">Time of Incident</p>
                                <p className="label-sub-header-4 mb-4">{incident?.incident_date?.split(" ")[1]}</p>

                                <p className="label-header-4 mb-2">Name of Instructor</p>
                                <p className="label-sub-header-4 mb-4">{incident?.instructor_one_name}</p>

                                <p className="label-header-4 mb-2">Name of Instructor</p>
                                <p className="label-sub-header-4 mb-4">{incident?.instructor_two_name}</p>

                                <p className="label-header-4 mb-2">Description of Incident</p>
                                <p className="label-sub-header-4 mb-4">{incident?.description}</p>
                        </div>
                        <div className="pt-[40px]">
                            <div className="flex flex-col lg:flex-row lg:items-center lg:self-start gap-4 mb-6">
                                <EditButton title='Mark as Resolved' width="w-[254px]" height="h-[52px]"  onClick={() => handleOpenManageModal("change")} />
                                <EditButton title='Escalate Issue' width="w-[254px]" height="h-[52px]"  onClick={() => handleOpenManageModal("change")} />

                            </div> 
                            <div className=" mt-6 grid grid-cols-3 gap-2 justify-end">
                                {incident?.files?.map((img, index) => (
                                    <img 
                                        key={index} 
                                        src={"/storage/" + img?.path} 
                                        alt={img?.file_name} 
                                        className="rounded-lg object-cover h-48 w-48" 
                                    />
                                ))}
                            </div>                                                    
                        </div>
                       
                    </div>
                </div>
                <ManageModal
                isOpen={isManageModalOpen}
                onClose={handleCloseModal}
                checkedState={checkedState}
                mode={modalMode}
                users={users}

            />
            </div>

        </AuthenticatedLayout>
    );
};

export default IncidentsIndex;
