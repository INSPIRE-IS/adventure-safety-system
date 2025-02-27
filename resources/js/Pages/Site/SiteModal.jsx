import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import MultiSelect from "@/Components/MultiSelect";
import CustomDropzoneUploader1 from "@/Components/CustomDropzoneUploader1";

const SiteModal = ({ isOpen, onClose, mode, site = null, typeActivities }) => {
    console.log("mode", mode);
    console.log("typeActivities", typeActivities);
    const [preview, setPreview] = useState("./images/logo.png");
    const [logo, setLogo] = useState("");

    const { data, setData, post, put, errors, reset, processing } = useForm({
        id: "",
        name: "",
        description:"",
        location: "",
        phone: "",
        email: "",
        website: "",
        image: "",
        type_activities: [],
    });

    useEffect(() => {
        if (site && mode !== "create") {
            setData({
                id: site.id || "",
                name: site.name || "",
                description: site.description || "",
                location: site.location || "",
                phone: site.phone || "",
                email: site.email || "",
                website: site.website || "",
                image: site.image || "",
                //typeActivity_id: site.type_activity_id || "",
                type_activities: site.type_activities.map((type_activity) => type_activity.id),
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,site]);

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

        // if(logo != null){
        //     //data.image = logo;
        //     setData((prev) => ({ ...prev, ['image']: logo }));
        // }


        if (mode === "create") {
            post(route("sites.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
           //console.log("data", data);
            put(route("sites.update", site.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };
    const handleSubmit1 = (e) => {
        e.preventDefault();
    
        const formData = new FormData();
        formData.append("id", data.id);
        formData.append("name", data.name);
        formData.append("description", data.description);
        formData.append("location", data.location);
        formData.append("phone", data.phone);
        formData.append("email", data.email);
        formData.append("website", data.website);
        if (data.image instanceof File) {
            formData.append("image", data.image);
        } else {
            console.warn("Image is NOT a file:", data.image);
        }
    
        data.type_activities.forEach((id) => formData.append("type_activities[]", id));
    
        console.log("Submitting FormData:", [...formData]);
    
        if (mode === "create") {
            post(route("sites.store"), {
                data: formData,
                headers: { "Content-Type": "multipart/form-data" }, // Important for file uploads
                onSuccess: () => {
                    reset();
                    onClose();
                },
            });
        } else if (mode === "edit") {
            console.log("Submitting FormData:", formData);
            put(route("sites.update", site.id), {
                data: formData,
                headers: { "Content-Type": "multipart/form-data" }, // Important for file uploads
                onSuccess: () => {
                    reset();
                    onClose();
                },
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
                    {mode === "create" && "Create Site"}
                    {mode === "edit" && "Edit Site"}
                    {mode === "view" && "View Site"}
                </h2>
                <form onSubmit={handleSubmit}>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <InputText
                        title="Name"
                        field="name"
                        value={data.name}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Description"
                        field="description"
                        value={data.description}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Location"
                        field="location"
                        value={data.location}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Phone"
                        field="phone"
                        value={data.phone}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Email"
                        field="email"
                        value={data.email}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <InputText
                        title="Website"
                        field="website"
                        value={data.website}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    {/* <InputText
                        title="Image"
                        field="image"
                        value={data.image}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    /> */}
                    </div>   
                    <div className="flex flex-col w-full sm:flex-row items-center justify-center ">
      
                        <div className="flex flex-col">
                            <img
                            src={ preview }
                            alt="logo"
                            className="w-28 h-28  mb-4 mr-6 object-scale-down"
                            />            
                        </div>

                        <CustomDropzoneUploader1 onUpload={HandleFile} />                    
                    </div>                 
                    <MultiSelect
                        title="Type Activities"
                        fields={typeActivities}
                        allFields={data.type_activities}
                        mode={mode}
                        handleIsChecked={handleIsChecked}
                        handleCheckChange={handleCheckChange}
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

export default SiteModal;
