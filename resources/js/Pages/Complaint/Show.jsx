import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from "@inertiajs/react";
import EditButton from '@/Components/EditButton';
import ManageModal from '@/Components/ManageModal';
import lookupIcon from '@/Utils/lookupIcons';
import EditButton2 from '@/Components/EditButton2';


const ComplaintsIndex = ({auth, complaint, can, users, complaint_id, category_id }) => {
const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
console.log('complaint',complaint);

const handleOpenManageModal = (mode) => {

    if(complaint_id == null || category_id == null){
        alert("Please select at least one complaint");
        return;
    }
    setModalMode(mode);
    setCheckedState({complaint_id,category_id});
    setIsManageModalOpen(true);
};

const handleCloseModal = () => {
    setIsManageModalOpen(false);
};



    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Complaints" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <EditButton2 
                    title="Back to Complaint Management" 
                    width="w-[254px]" 
                    height="h-[52px]" 
                    icon={lookupIcon("circleLeft")} 
                    onClick={() => router.get(route('complaints.index'))}
                />
                </div>
                <header className="mb-6 flex items-center justify-between w-[100%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">Complaint {complaint?.complaint_date?.split(" ")[0]}</h1>                    
                    </div>
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        {can.complaint_edit_reminder && (
                        <EditButton title='Send Reminder' width="w-[214px]" height="h-[52px]" icon="./images/add-alert-clock-icon.svg" onClick={() => handleOpenManageModal("reminder")} />
                        )}
                        {can.complaint_edit_inspector && (
                        <EditButton title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon="./images/add-user-icon-2.svg" onClick={() => handleOpenManageModal("assign")} />
                        )}
                        {can.complaint_edit_date && (
                        <EditButton title='Update Complaint Date' width="w-[254px]" height="h-[52px]" icon="./images/clock-setting-gear-icon.svg" onClick={() => handleOpenManageModal("change")} />
                        )}
                    </div>
                </header>
                <div className=" pl-[40px] pr-[32px] py-[41px] rounded-3xl bg-white">
                    <div className="grid grid-cols-2 gap-2 mb-4">
                        <div className="col-span-1 ">
                            <div className='mb-4'>
                            <p className="view-header-2 mb-2">Name</p>
                            <p className="view-header-2-light">{complaint?.name}</p>
                            </div>
                            <div className='mb-4'>
                            <p className="label-header-4 mb-2">Contact Phone</p>
                            <p className="view-header-2-light">{complaint?.phone}</p>
                            </div>
                        </div>
                        <div className="col-span-1">
                            <div className='mb-4'>
                            <p className="label-header-4 mb-2">Date of Complaint</p>
                            <p className="view-header-2-light">{complaint?.complaint_date?.split(" ")?.[0]}</p>
                        </div>                                    
                        <div className='mb-4'>
                            <p className="view-header-2 mb-2">Contact Email</p>
                            <p className="view-header-2-light">{complaint?.email}</p>
                            </div>
                        </div>
                    </div>
                    <div className='flex flex-col'>
                        <p className="view-header-2 mb-2">Description of Complaint
                        </p>
                        <p className="label-comment-text">
                        {complaint?.description}
                        </p>                                         
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

export default ComplaintsIndex;
