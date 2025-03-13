import React, { useEffect, useState } from "react";
import { router, useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomButton from "@/Components/CustomButton";
import CustomSelect1 from "./CustomSelect1";
import { useToast } from "@/context/ToastProvider";

const ManageModal = ({ isOpen, onClose,mode, users, checkedState }) => {
    const { addToast } = useToast();
    console.log("mode", mode);
    console.log("checkedState", checkedState);
    const { data, setData, post, put, errors, reset } = useForm({
        date:"",
        assign:"",
        reminder:"",
        checkedState: [], // Stores barcodes or numbered items
    });

    useEffect(() => {

            setData({
                mode: mode,
                date: mode == "change" ? new Date().toISOString().split("T")[0] : "",
                assign: "",
                reminder:"",
                checkedState: checkedState || [], 
            });

    }, [mode,checkedState]);

    const handleChange = (target, value) => {
        setData(target, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
            console.log('handleSubmit checkedState', data);
            post(route("inspections.manage",checkedState), {
                onSuccess: (res) => {
                    console.log('success',res);
                    addToast("success", "Submitted Successfully");
                    onClose();                    
                    reset();

                },
                onError: (err) => {
                    console.log('error',err);
                    addToast("error", "Something went wrong");
                },
            });

    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl max-h-[95%] overflow-y-auto">
                <h2 className="text-xl font-bold mb-4">
                    Update Inspection
                </h2>
                <form onSubmit={handleSubmit}>

                    {/* Identification Section */}
                    <div className="mt-4">

                        {mode == "change" && (
                            <>
                            <label className="block text-md font-medium text-gray-700">
                                New Date
                            </label>                            
                            <div className="flex items-center space-x-2 mt-2">
                                <input
                                    type="date"
                                    name="date"
                                    className="border border-gray-300 rounded p-2 flex-grow"
                                    value={data?.date}
                                    onChange={(e) => handleChange(e.target.name, e.target.value)}
                                    placeholder="Enter date"
                                />
                            </div>                            
                            </>
                        )}
                        {mode == "assign" && (
                            <>
                                <label className="block text-md font-medium text-gray-700">
                                Assign New Inspector
                                </label>
                                <CustomSelect1
                                label="Assign New Inspector"
                                name="assign"
                                value={data?.assign}
                                mode={mode}
                                onChange={(e) => handleChange(e.target.name, e.target.value)}
                                error={errors.assign}
                                options={users}
                            />                            
                            </>
                        )}
                        {mode == "reminder" && (
                            <label className="block text-md font-medium text-gray-700">
                            Send Reminder
                            </label>
                        )}
                        {/* List of added identifications */}
                        {/* {checkedState.length > 0 && (
                            <ul className="mt-3 space-y-2">
                                {data?.checkedState.map((item, index) => (
                                    <li
                                        key={index}
                                        className="flex items-center justify-start p-2 bg-gray-100 rounded"
                                    >
                                        <span>Inspection ID {item.inspection_id} </span>                                        
                                        <span>Category ID {item.category_id} </span>

                                    </li>
                                ))}
                            </ul>
                        )} */}
                    </div>

                    <div className="flex justify-end space-x-2 mt-4">
                        <CustomButton
                            onClick={onClose}
                            title="Cancel"
                            btnColor="bg-gray-300"
                            hoverTextColor="text-white"
                            hoverColor="bg-gray-400"
                        />

                        {mode !== "view" && (
                            <CustomButton
                                onClick={handleSubmit}
                                title="Submit"
                                btnColor="bg-primary"
                                hoverTextColor="text-white"
                                hoverColor="bg-primary-dark"
                            />
                        )}
                    </div>
                </form>
            </div>
        </div>
    );
};

export default ManageModal;
