import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm , usePage } from "@inertiajs/react";
import Pagination from '@/Components/Pagination';
import InspectionModal from './InspectionModal';
import SearchBar from '@/Components/SearchBar';
import AddButton from '@/Components/AddButton';
import EditButton from '@/Components/EditButton';
import CustomSelect2 from '@/Components/CustomSelect2';
import ManageModal from '@/Components/ManageModal';
import ElementModal from './ElementModal';



const InspectionsIndex = ({auth, inspection, inspection_details, can, users,inspection_id,category_id }) => {
const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
console.log('inspection_details',inspection_details);
console.log('inspection',inspection);
function processInspectionData(data) {
    let count = 0;
    let allImages = [];

    data.forEach(item => {
        // Count non-null conditions
        if (item.condition !== null) {
            count++;
        }

        // Collect all images
        if (item.files && Array.isArray(item.files)) {
            allImages.push(...item.files.map(file => file));
        }
    });

    return { count, allImages };
}
const result = processInspectionData(inspection_details);


const handleOpenManageModal = (mode) => {

    if(inspection_id == null || category_id == null){
        alert("Please select at least one inspection");
        return;
    }
    setModalMode(mode);
    setCheckedState({inspection_id,category_id});
    setIsManageModalOpen(true);
};

const handleCloseModal = () => {
    setIsManageModalOpen(false);
};



    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Inspections" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <EditButton title='Back to Inspection Management' width="w-[254px]" height="h-[52px]" icon="./images/back-icon.svg"  onClick={() => router.get(route('inspections.index'))} />
                </div>
                <header className="mb-6 flex items-center justify-between w-[100%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                    </div>
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        {can.inspection_edit_reminder && (
                        <EditButton title='Send Reminder' width="w-[214px]" height="h-[52px]" icon="./images/add-alert-clock-icon.svg" onClick={() => handleOpenManageModal("reminder")} />
                        )}
                        {can.inspection_edit_inspector && (
                        <EditButton title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon="./images/add-user-icon-2.svg" onClick={() => handleOpenManageModal("assign")} />
                        )}
                        {can.inspection_edit_date && (
                        <EditButton title='Update Inspection Date' width="w-[254px]" height="h-[52px]" icon="./images/clock-setting-gear-icon.svg" onClick={() => handleOpenManageModal("change")} />
                        )}
                    </div>
                </header>

                {/* Inspections Table */}
                <div className=" pl-[40px] pr-[32px] pt-5 rounded-3xl bg-white">
                    <div className='flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6'>
                        <div className="pt-[49px]  pb-[32px] ">               
                            <h3 className="label-alt-header-3">{inspection_details?.[0].category_item.name}</h3>                    
                        </div>
                        <div className="flex flex-col lg:flex-row lg:items-center lg:justify-start gap-4 mb-6">
                        <EditButton title='Mark as Resolved' width="w-[254px]" height="h-[52px]"  onClick={() => handleOpenManageModal("change")} />
                        <EditButton title='Escalate Issue' width="w-[254px]" height="h-[52px]"  onClick={() => handleOpenManageModal("change")} />
                        </div>                        
                    </div>

                    <div className="overflow-x-auto  ">
                        <div className="">
                            <div className="mt-4 grid grid-cols-3 gap-4 text-sm">
                                <div>
                                <p className="label-header-4">Inspector:</p>
                                <p className="label-sub-header-4">{inspection?.assigned_to?.name}</p>
                                </div>
                                <div>
                                <p className="label-header-4">Date Completed:</p>
                                <p className="label-sub-header-4">{inspection?.complete_date}</p>
                                </div>
                                <div>
                                <p className="label-header-4">Issues Reported:</p>
                                <p className="label-sub-header-4">{result.count}</p>
                                </div>
                            </div>
                            <div className="grid grid-cols-3 gap-4">
                                {/* Left Section (1/3) */}
                                <div className="col-span-1 mt-6">
                                    <p className="label-header-4">General Comments</p>
                                    <p className="label-comment-text">{inspection?.closing_comment}</p>
                                </div>

                                {/* Right Section (2/3) */}
                                <div className="col-span-2 mt-6 grid grid-cols-3 gap-2 justify-end">
                                    {result.allImages.map((img, index) => (
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

export default InspectionsIndex;
