import React, { useEffect, useState } from "react";
import { router, useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import ImageUpload from "@/Components/ImageUpload";
import CustomSelect1 from "@/Components/CustomSelect1";
import CustomButton from "@/Components/CustomButton";


const ElementModal = ({ isOpen, onClose, mode, categoryItem = null, type_inspections, type_bools, team, type_item }) => {
    console.log("mode", mode);
    console.log("categoryItem", categoryItem);
    //alert(type_item);
    //console.log("type_bools", type_bools);
    const currentImage = categoryItem?.path; 
    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        type_inspection_id: "",        
        site_id: team?.id,
        in_use_date: new Date().toISOString().split("T")[0],
        out_use_date: "",
        certificate_recieved_id: 2, 
        identifications: [], // Stores barcodes or numbered items
    });
    const [newIdentification, setNewIdentification] = useState("");

    useEffect(() => {
        if (categoryItem && mode !== "create") {
            setData({
                name: categoryItem.name || "",
                type_inspection_id: categoryItem.type_inspection_id || "",
                site_id: categoryItem.site_id || "",
                in_use_date: categoryItem.in_use_date || "",
                out_use_date: categoryItem.out_use_date || "",
                certificate_recieved_id: categoryItem.certificate_recieved_id || "",
                identifications: categoryItem.identifications.map(item => {
                    //console.log('item', item);
                    return typeof item === "string" ? item : item.name || JSON.stringify(item)
                }) || [], // Load existing identifications
            });
            console.log('data', data);
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,categoryItem]);

    const handleChange = (target, value) => {
        console.log('target', target);
        console.log('value', value);
        console.log('data---------------', data);
        setData(target, value);
        //setData("name", e.target.value);
    };

    const handleAddIdentification = () => {
        if (newIdentification.trim() !== "") {
            setData("identifications", [...data.identifications, newIdentification.trim()]);
            setNewIdentification(""); // Reset input field
        }
    };

    const handleRemoveIdentification = (index) => {
        setData(
            "identifications",
            data.identifications.filter((_, i) => i !== index)
        );
    };


    const handleImageUpload = (file) => {
        //const files = event.target.files || event.dataTransfer.files;
        //const file = files[0];

        //console.log("profile photo", file);
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File size must be less than 10MB.');
            return;
        }

        const formData = new FormData();
        formData.append("image", file);
        formData.append("site_id", categoryItem.site_id);
        formData.append("table_name", "category_items");	
        formData.append("file_name", file.name);
        formData.append("type_file_id", 8);
        formData.append("row", categoryItem.id);
        formData.append("path", file.path);

        router.post(route("files.store"), formData, {
          onSuccess: () => {
            console.log('Success');
          },
          onError: (errors) => {
            console.log('Errors:', errors);
          },
          onFinish: () => {
            // Always called after success or error
            console.log('Request finished');
          },
          preserveScroll: true,  // This will preserve scroll position after the request
        } );
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        //console.log('data', data);return;
        if (mode === "create") {
            post(route("category-items.store"), {

                onSuccess: () => {reset();onClose()},
                onError: (err) => {console.log(err)},
            });
        } else if (mode === "edit") {
            put(route("category-items.update", categoryItem.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 ">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl max-h-[95%] overflow-y-auto">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Element Item"}
                    {mode === "edit" && "Edit Element Item"}
                    {mode === "view" && "View Element Item"}
                </h2>
                <form onSubmit={handleSubmit}>
                    <InputText
                        title="Name"
                        field="name"
                        value={data.name}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />
                    <CustomSelect1
                        label="Type"
                        name="type_inspection_id"
                        value={data?.type_inspection_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_inspection_id}
                        options={type_inspections}
                    />

                                        {/* Identification Section */}
                    <div className="mt-4 mb-4">
                        <label className="block input-header">
                            Add Identification
                        </label>
                        <div className="flex items-center space-x-2 mt-2">
                            <input
                                type="text"
                                className="border border-gray-300 rounded p-2 flex-grow"
                                value={newIdentification}
                                onChange={(e) => setNewIdentification(e.target.value)}
                                placeholder="Enter barcode or item number"
                            />

                            <CustomButton
                                onClick={handleAddIdentification}
                                title="Add"
                                btnColor="bg-primary"
                                hoverTextColor="text-white"
                                hoverColor="bg-primary-dark"
                            />
                        </div>

                        {/* List of added identifications */}
                        {data.identifications.length > 0 && (
                            <ul className="mt-3 space-y-2">
                                {data.identifications.map((item, index) => (
                                    <li
                                        key={index}
                                        className="flex items-center justify-between p-2 bg-gray-100 rounded"
                                    >
                                        <span>{item}</span>
                                        <CustomButton
                                            onClick={() => handleRemoveIdentification(index)}
                                            title="Remove"
                                            btnColor="bg-gray-300"
                                            hoverTextColor="text-white"
                                            hoverColor="bg-gray-400"
                                        />
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>

                    {type_item == 1 &&(
                        <> 
                            <CustomSelect1
                                label="Certificate Recieved"
                                name="certificate_recieved_id"
                                mode={mode}
                                value={data?.certificate_recieved_id}
                                onChange={(e) => handleChange(e.target.name, e.target.value)}
                                error={errors.certificate_recieved_id}
                                options={type_bools}
                            />

                            <div className="flex justify-start">
                                <ImageUpload
                                mode={mode}
                                currentImage={currentImage}
                                onUpload={handleImageUpload}
                                />
                            </div>                        
                        </>
                    )}

                    <div className="flex justify-end space-x-2">
                    <CustomButton
                            onClick={onClose}
                            title={"Cancel"}
                            btnColor="bg-gray-300"
                            hoverTextColor="text-white"
                            hoverColor="bg-gray-400"
                            />

                        {/* <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button> */}
                        {mode !== "view" && (
                            <CustomButton
                            onClick={handleSubmit}
                            title={mode === "create" ? "Create" : "Update"}
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

export default ElementModal;
