import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from "@inertiajs/react";
import EditButton from '@/Components/EditButton';
import ManageModal from '@/Components/ManageModal';
import EditButton2 from '@/Components/EditButton2';
import lookupIcon from '@/Utils/lookupIcons';
import { useToast } from '@/context/ToastProvider';



const InspectionsIndex = ({auth, inspection, inspection_details, can, users,inspection_id,category_id }) => {
    const { addToast } = useToast();
    const { data, setData, post, errors, reset } = useForm({
        inspection_id: 0,
        category_id: 0,
    });

const [isManageModalOpen, setIsManageModalOpen] = useState(false);
const [modalMode, setModalMode] = useState("create");
const [checkedState, setCheckedState] = useState([]);
console.log('inspection_details',inspection_details);
console.log('inspection',inspection);
useEffect(() => {
    setData({
        inspection_id: inspection_id,
        category_id: category_id,
    });
}, []);



// function processInspectionData(insData) {
//     let count = 0;
//     let allImages = [];

//     insData.forEach(item => {
//         // Count non-null conditions
//         if (item.condition !== null || item.files.length > 0) {
//             count++;
//         }

//         // Collect all images
//         if (item.files && Array.isArray(item.files)) {
//             allImages.push(...item.files.map(file => file));
//         }
//     });

//     return { count, issues, allImages };
// }
function processInspectionData(insData) {
    let groupedData = {};

    insData.forEach(item => {
        const categoryName = item.category_item.name; // Group by category name
        const categoryDetailName = item.category_detail.name; // Include category detail name

        if (!groupedData[categoryName]) {
            groupedData[categoryName] = {
                category_name: categoryName,
                issues: [],
                count: 0
            };
        }

        if (item.condition !== null || (item.files && item.files.length > 0)) {
            groupedData[categoryName].count++;
            groupedData[categoryName].issues.push({
                category_detail_name: categoryDetailName,
                condition: item.condition,
                files: item.files.map(file => ({
                    id: file.id,
                    file_name: file.file_name,
                    file_type: file.file_type,
                    path: file.path,
                    created_at: file.created_at
                }))
            });
        }
    });

    return Object.values(groupedData); // Convert object back to an array
}
const result = processInspectionData(inspection_details);
console.log('result', result);
console.log('inspection_details', inspection_details);


const handleOpenManageModal = (mode) => {

    if(inspection_id == null || category_id == null){
        alert("Please select at least one inspection");
        return;
    }
    setModalMode(mode);
    setCheckedState({inspection_id,category_id});
    setIsManageModalOpen(true);
};

const handleResolveIssue = (inspection_id,category_id) => {
    setCheckedState({inspection_id,category_id});
    post(route("inspections.resolve"), {

        onSuccess: () => {addToast("success", "Issue Resolved")},
        onError: () => {addToast("error", "Issue Resolved Failed")},
    });
};

const handleEscalateIssue = (inspection_id,category_id) => {
    setCheckedState({inspection_id,category_id});
    post(route("inspections.escalate"), {

        onSuccess: () => {addToast("success", "Issue Escalated Successfully!");},
        onError: () => {addToast("error", "Issue Escalation Failed");},
    });
};



const handleCloseModal = () => {
    setIsManageModalOpen(false);
};

    return (
        <AuthenticatedLayout user={auth.user} is_admin={auth?.is_admin}>
            <Head title="Inspections" />
            <div className="container mx-auto py-6">
                {/* Header */}
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <EditButton2
                        title="Back to Inspection Management" 
                        width="w-[254px]" 
                        height="h-[52px]" 
                        icon={lookupIcon("circleLeft")} 
                        onClick={() => router.get(route('inspections.index'))}
                    />
                </div>
                <header className="mb-6 flex items-center justify-between w-[100%]">
                    <div className="flex flex-col  items-right">               
                        <h1 className="view-header">{auth?.team?.name ?? 'Site Name'}</h1>                    
                    </div>
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        {can.inspection_edit_reminder && (
                        <EditButton2 title='Send Reminder' width="w-[214px]" height="h-[52px]" icon={lookupIcon("add_alert_clock")} onClick={() => handleOpenManageModal("reminder")} />
                        )}
                        {can.inspection_edit_inspector && (
                        <EditButton2 title='Assign New Inspector' width="w-[244px]" height="h-[52px]" icon={lookupIcon("add_user")} onClick={() => handleOpenManageModal("assign")} />
                        )}
                        {can.inspection_edit_date && (
                        <EditButton2 title='Update Inspection Date' width="w-[254px]" height="h-[52px]" icon={lookupIcon("clock_setting_gear")} onClick={() => handleOpenManageModal("change")} />
                        )}
                    </div>
                </header>

                {/* Inspections Table */}
                <div className=" pl-[40px] pr-[32px] pt-5 rounded-3xl bg-white">
                    <div className='flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6'>
                        <div className="pt-[49px]  pb-[32px] ">               
                            <h3 className="label-alt-header-3">Inspection #: {inspection.id}</h3>                    
                        </div>
                        <div className="flex flex-col lg:flex-row lg:items-center lg:justify-start gap-4 mb-6">
                        <EditButton title='Mark as Resolved' width="w-[254px]" height="h-[52px]"  onClick={handleResolveIssue} />
                        <EditButton title='Escalate Issue' width="w-[254px]" height="h-[52px]"  onClick={handleEscalateIssue} />
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
                                <div className="col-span-2 mt-6 mb-6  gap-2 justify-end">
                                    {result?.map((category, categoryIndex) => (
                                        <div key={categoryIndex} className="flex flex-col gap-4">
                                            {/* Category Name Header */}
                                            <h2 className="text-xl font-semibold">Element Name: {category.category_name} <small>issues: {category.count}</small></h2>
                                            
                                            {/* Iterate Over Issues Inside Each Category */}
                                            {category.issues.map((issue, issueIndex) => (
                                                <div key={issueIndex} className="flex flex-col gap-2 border-b p-4 rounded-lg shadow-sm">
                                                    <p className="label-header-4">Reference #: {issue.category_detail_name}</p>
                                                    <p className="label-comment-text">Condition: {issue.condition ?? 'none specified'}</p>
                                                    
                                                    {/* Display Images */}
                                                    <div className="grid grid-cols-2 gap-2">
                                                        {issue.files?.map((file, fileIndex) => (
                                                            <img 
                                                                key={fileIndex} 
                                                                src={file?.path} 
                                                                alt={file?.file_name} 
                                                                className="rounded-lg object-cover h-48 w-48" 
                                                            />
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
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
