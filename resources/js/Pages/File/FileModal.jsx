import React, { useEffect } from "react";
import { router, useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import ImageUpload from "@/Components/ImageUpload";
import CustomSelect1 from "@/Components/CustomSelect1";
import CustomInput1 from "@/Components/CustomInput1";

const FileModal = ({ isOpen, onClose, mode, file = null, typeFiles, teams, auth }) => {
    console.log("mode", mode);
    console.log("file", file);
    console.log("teams", teams);
    console.log("auth", auth);


    const currentImage = file?.path; 

    const { data, setData, post, put, errors, reset, processing } = useForm({
        expirey: "",
        type_file_id: 0,
    });
    useEffect(() => {
        if (file && mode !== "create") {
            setData({
                type_file_id: file.type_file_id || "",
                expirey: file.expirey || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,file]);

    const handleChange = (target, value) => {
        console.log("handleChange", target, value);
        setData(target, value);
        //setData("name", e.target.value);
    };
    const handleImageUpload = (file) => {
        //const files = event.target.files || event.dataTransfer.files;
        //const file = files[0];

        //console.log("profile photo", file);
        if(data.type_file_id == 0 || data.type_file_id == undefined){
            alert("Please select a file type");
            return;
        }
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File size must be less than 10MB.');
            return;
        }

        const formData = new FormData();
        formData.append("image", file);
        formData.append("site_id", auth?.team.id);
        formData.append("table_name", "sites");	
        formData.append("file_name", file.name);
        formData.append("file_type", file.type);
        formData.append("type_file_id", data.type_file_id);
        formData.append("table_row_id", auth?.team.id);
        formData.append("path", file.path);

        router.post(route("files.store"), formData, {
          onSuccess: () => {
            console.log('Success');
            onClose();
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
        // if (mode === "create") {
        //     post(route("files.store"), {

        //         onSuccess: () => {reset();onClose()},
        //     });
        // } else if (mode === "edit") {
        //     put(route("files.update", file.id), {
        //         onSuccess: () => {reset();onClose()},
        //     });
        // }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create File "}
                    {mode === "edit" && "Edit File "}
                    {mode === "view" && "View File "}
                </h2>
                <form onSubmit={handleSubmit}>
                    <CustomSelect1
                        label="Type File"
                        name="type_file_id"
                        value={data?.type_file_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_file_id}
                        options={typeFiles}
                    />
                    <CustomInput1 
                        label="Expiry (empty for no expiry)"
                        type="date"
                        name="expirey"
                        value={data?.expirey}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                    />
                    <div className="flex justify-start">
                        <ImageUpload
                        mode={mode}
                        currentImage={currentImage}
                        onUpload={handleImageUpload}
                        />
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

export default FileModal;
