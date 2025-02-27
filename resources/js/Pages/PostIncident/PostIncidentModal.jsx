import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomSelect1 from "@/Components/CustomSelect1";
//import { format } from 'date-fns';
import CustomDatePicker from "@/Components/CustomDatepicker";
import MultiSelect from "@/Components/MultiSelect";
import CustomInput1 from "@/Components/CustomInput1";

const PostIncidentModal = ({ auth, isOpen, onClose, mode, postincident = null,site, post_incident_schedules, sites, users, postIncident }) => {
    console.log("mode", mode);


    const { data, setData, post, put, errors, reset, processing } = useForm({
        due_date: "",
        complete_date: "",
        assigned_to_id: auth?.user?.id,
        report_file_id: null,
        post_incident_schedule_id: null,
        site_id: null,


    });
    useEffect(() => {
        if (postincident && mode !== "create") {
            setData({
                due_date: postincident.due_date || "",
                complete_date: postincident.complete_date || "",
                assigned_to_id: postincident.assigned_to_id || "",
                report_file_id: postincident.report_file_id || "",
                post_incident_schedule_id: postincident.post_incident_schedule_id || "",
                site_id: postincident.site_id || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
            // setData({
            //     name: auth?.team?.name || "",
            // });
        }
    }, [mode,postincident]);


    function formatDate(isoDate) {
        const date = new Date(isoDate);
        //console.log('date', date); return;
        const day = String(date.getDate()).padStart(2, '0');   // Get day and pad with leading zero
        const month = String(date.getMonth() + 1).padStart(2, '0');  // Get month (months are 0-indexed, so add 1) and pad
        const year = date.getFullYear();   // Get full year
    
        return `${day}-${month}-${year}`;  // Return formatted date as DD-MM-YYYY
    }

    const handleChange = (target, value) => {
        //const formattedValue = target === 'active_date' ? format(value, 'dd-MM-yyyy') : value;
        console.log(value);
        //console.log(formattedValue);
        //setData(target, formattedValue);
        setData({ ...data, [target]: value });
        console.log("data", data);
        //setData("name", e.target.value);
    };

    const handleIsChecked = (check_array, check_id) => {
        console.log("check_array", check_array);
        console.log("check_id", check_id);
        return Array.isArray(check_array) && check_array.includes(check_id);
    };
    const handleCheckChange = (event,check_id) => {
        console.log("event", event);
        console.log("check_id", check_id);
        const isChecked = event.target.checked;
        console.log("isChecked", isChecked);
        setData((prevState) => {
            const updatedCategoryItems = isChecked
                ? [...prevState.category_items, check_id]
                : prevState.category_items.filter((id) => id !== check_id);
        
            return { ...prevState, category_items: updatedCategoryItems };
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log("data", data);
        if (mode === "create") {
            post(route("post-incidents.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("post-incidents.update", postincident.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Post Incident"}
                    {mode === "edit" && "Edit Post Incident"}
                    {mode === "view" && "View Post Incident"}
                </h2>
                <form onSubmit={handleSubmit}>               
                    <label className="block text-lg font-semibold mb-4">
                    {site}
                    </label>
                    <CustomDatePicker
                        label="Due Date"
                        name="due_date"
                        value={data.due_date}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <CustomDatePicker
                        label="Complete Date"
                        name="complete_date"
                        value={data.complete_date}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <CustomSelect1
                        label="Post Incident Schedule"
                        name="post_incident_schedule_id"
                        value={data?.post_incident_schedule_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.post_incident_schedule_id}
                        options={post_incident_schedules}
                    />
                    <CustomSelect1
                        label="Site"
                        name="site_id"
                        value={data?.site_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.site_id}
                        options={sites}
                    />

                    <CustomSelect1
                        label="User"
                        name="assigned_to_id"
                        value={data?.assigned_to_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.assigned_to_id}
                        options={users}
                    />

                    <div className="flex justify-end space-x-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                        {mode !== "view" && (
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                disabled={processing}
                            >
                                {mode === "create" ? "Create" : "Update"}
                            </button>
                        )}
                    </div>
                </form>
            </div>
        </div>
    );
};

export default PostIncidentModal;
