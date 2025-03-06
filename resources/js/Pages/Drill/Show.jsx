import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from "@inertiajs/react";
import EditButton from '@/Components/EditButton';
import ManageModal from '@/Components/ManageModal';




const DrillsIndex = ({auth, drill, can, users, drill_id, category_id }) => {
const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
console.log('drill',drill);



const handleOpenManageModal = (mode) => {

    if(drill_id == null || category_id == null){
        alert("Please select at least one drill");
        return;
    }
    setModalMode(mode);
    setCheckedState({drill_id,category_id});
    setIsManageModalOpen(true);
};

const handleCloseModal = () => {
    setIsManageModalOpen(false);
};



    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Drills" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <EditButton title='Back to Drill Management' width="w-[254px]" height="h-[52px]" icon="./images/back-icon.svg"  onClick={() => router.get(route('drills.index'))} />
                </div>
                <header className="mb-6 flex items-center justify-between w-[100%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                    </div>
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        {can.drill_edit_reminder && (
                        <EditButton title='Send Reminder' width="w-[214px]" height="h-[52px]" icon="./images/add-alert-clock-icon.svg" onClick={() => handleOpenManageModal("reminder")} />
                        )}
                        {can.drill_edit_inspector && (
                        <EditButton title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon="./images/add-user-icon-2.svg" onClick={() => handleOpenManageModal("assign")} />
                        )}
                        {can.drill_edit_date && (
                        <EditButton title='Update Drill Date' width="w-[254px]" height="h-[52px]" icon="./images/clock-setting-gear-icon.svg" onClick={() => handleOpenManageModal("change")} />
                        )}
                    </div>
                </header>

                {/* Drills Table */}
                <div className=" pl-[40px] pr-[32px] py-[41px] rounded-3xl bg-white">
                    <div className='flex flex-col lg:flex-row lg:items-center lg:justify-between mb-2'>
                        <div className="  pb-[18px] ">               
                            <h3 className="label-alt-header-3">{drill?.type_drill?.name}</h3>                    
                        </div>                      
                    </div>

                            <div className="grid grid-cols-3 gap-2 mb-4">

                                <div className="col-span-1 ">
                                    <div className='mb-4'>
                                    <p className="view-header-2 mb-2">Inspector:</p>
                                    <p className="view-header-2-light">{drill?.assigned_to?.name}</p>
                                    </div>
                                    <div className='mb-4'>
                                    <p className="label-header-4 mb-2">Date Completed:</p>
                                    <p className="view-header-2-light">{drill?.complete_date}</p>
                                    </div>
                                </div>


                                <div className="col-span-2  grid grid-cols-3 gap-2 justify-start align-top">
                                    <div className='flex flex-col'>
                                        <p className="view-header-2 mb-2">General Comments:
                                        </p>
                                        <p className="label-comment-text">drill members

                                            {drill?.drill_members?.map((member) => {
                                                return (<span> {member.member_name} </span>);
                                            })}                                            
                                            are all compitent.
                                        </p>                                         
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

export default DrillsIndex;
