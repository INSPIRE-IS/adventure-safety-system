import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import MultiSelect from "@/Components/MultiSelect";
import CustomDropzoneUploader1 from "@/Components/CustomDropzoneUploader1";
import CustomSelect1 from "@/Components/CustomSelect1";
import CustomDatePicker from "@/Components/CustomDatepicker";
import CustomTextarea1 from "@/Components/CustomTextarea1";
import CustomDateTimePicker from "@/Components/CustomDateTimePicker1";

const IncidentModal = ({ isOpen, onClose, mode, incident = null, sites, typeIncidents,signatureFiles }) => {
    console.log("mode", mode);
    //console.log("typeActivities", typeActivities);
    const [preview, setPreview] = useState("./images/logo.png");
    const [logo, setLogo] = useState("");
    const [selectedDate, setSelectedDate] = useState(null);
    const [selectedTime, setSelectedTime] = useState(null);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        id: "",
        site_id: "",
        type_incident_id: "",
        incident_date: "",
        description: "",
        signature_file_id: "",
        incident_file_id: "",

    });

    useEffect(() => {
        if (incident && mode !== "create") {
            setData({
                id: incident?.id || "",
                site_id: incident?.site_id || "",
                type_incident_id: incident?.type_incident_id || "",
                incident_date: incident?.incident_date || "",
                description: incident?.description || "",
                signature_file_id: incident?.signature_file_id?.name || "",
                incident_file_id: incident?.incident_file_id?.name || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,incident]);

    const handleChange = (target, value) => {
        // if(target == 'phone'){
        //     //isValid = /^\+27\d{9}$/.test(value);
        //     isValid = /^\d{11}$/.test(value);
        //     errors.phone = isValid ? '' : 'Please enter a valid South African phone number';
        // }
        // if(target == 'email'){
        //     isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        //     errors.email = isValid ? '' : 'Please enter a valid email address';
        // }
        setData(target, value);
       //setData((prev) => ({ ...prev, [name]: value }));
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (mode === "create") {
            post(route("incidents.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
           //console.log("data", data);
            put(route("incidents.update", incident.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    const handleIsChecked = (check_array, check_id) => {
        //console.log("check_array", check_array);
        //console.log("check_id", check_id);
        return Array.isArray(check_array) && check_array.includes(check_id);
    };
    const handleCheckChange = (event,check_id) => {
        console.log("event", event);
        console.log("check_id", check_id);
        const isChecked = event.target.checked;
        console.log("isChecked", isChecked);
        setData((prevState) => {
            const updatedTypeActivities = isChecked
                ? [...prevState.type_activities, check_id]
                : prevState.type_activities.filter((id) => id !== check_id);
        
            return { ...prevState, type_activities: updatedTypeActivities };
        });
    };
    const HandleFile = (files) => {
        console.log(files);        
        const file = files[0];
        console.log("profile image", file);        
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('Error, File size must be less than 10MB.');
            return;
        }
        const previewUrl = URL.createObjectURL(file);
        setPreview(previewUrl);
        //setLogo(file);
        //setData((prev) => ({ ...prev, ['image']: file }));
        setData('image', file);
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Incident"}
                    {mode === "edit" && "Edit Incident"}
                    {mode === "view" && "View Incident"}
                </h2>
                <form onSubmit={handleSubmit}>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <CustomSelect1
                        label="Site"
                        name="site_id"
                        value={data?.site_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.site_id}
                        options={sites}
                        required
                    />
                    <CustomSelect1
                        label="Type Incident"
                        name="type_incident_id"
                        value={data?.type_incident_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_incident_id}
                        options={typeIncidents}
                        required
                    />
                    <CustomDatePicker
                        label="Incident Date"
                        name="incident_date"
                        value={data?.incident_date}
                        mode={mode}
                        onChange={handleChange}
                        error={errors.incident_date}
                        required
                    />
                    <CustomDateTimePicker
                    label="Select a Date"
                    type="date"
                    name="date"
                    value={selectedDate}
                    onChange={(name, value) => setSelectedDate(value)}
                    mode="edit"
                    />
                    <CustomDateTimePicker
                    label="Select a Time"
                    type="time"
                    name="time"
                    value={selectedTime}
                    onChange={(name, value) => setSelectedTime(value)}
                    mode="edit"
                    />
                    </div>
                        <CustomTextarea1
                        label="Description"
                        name="description"
                        value={data?.description}
                        mode={mode}
                        onChange={handleChange}
                        error={errors.description}
                        placeholder="Enter description"
                        rows={4}
                        />                    
                    <div className="flex flex-col w-full sm:flex-row items-center justify-center mb-2 ">
      
                        <div className="flex flex-col">
                            <img
                            src={ preview }
                            alt="logo"
                            className="w-28 h-28  mb-4 mr-6 object-scale-down"
                            />            
                        </div>

                        <CustomDropzoneUploader1 onUpload={HandleFile} />                    
                    </div> 
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

export default IncidentModal;
