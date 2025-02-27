import React, { useEffect } from "react";
import { router, useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import ImageUpload from "@/Components/ImageUpload";
import CustomInput1 from "@/Components/CustomInput1";
import CustomSelect1 from "@/Components/CustomSelect1";
import CustomButton from "@/Components/CustomButton";
import CustomDatePicker from "@/Components/CustomDatepicker";


const typeOptions = [
    { value: "1", label: "Mr" },
    { value: "2", label: "Mrs" },
    { value: "3", label: "Miss" },
    { value: "4", label: "Dr" },
  ];

const CategoryItemModal = ({ isOpen, onClose, mode, categoryItem = null, type_inspections, teams, type_bools }) => {
    console.log("mode", mode);
    console.log("categoryItem", categoryItem);
    //console.log("type_bools", type_bools);

    const currentImage = categoryItem?.path; 

    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        type_inspection_id: "",        
        site_id: "",        
        in_use_date: "",
        out_use_date: "",
        certificate_recieved_id: "", 

    });
    useEffect(() => {
        if (categoryItem && mode !== "create") {
            setData({
                name: categoryItem.name || "",
                type_inspection_id: categoryItem.type_inspection_id || "",
                site_id: categoryItem.site_id || "",
                in_use_date: categoryItem.in_use_date || "",
                out_use_date: categoryItem.out_use_date || "",
                certificate_recieved_id: categoryItem.certificate_recieved_id || "",
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
    const onChange = (target, value) => {
        // console.log('name', name);
        // console.log('value', value);
        // console.log('e', e);
        console.log('data---------------', data);
        setData(target, value);
    };
    // const handleChange = (e) => {
    //     const { name, value } = e.target;
    //     setData((prev) => ({
    //       ...prev,
    //       [name]: value,
    //     }));
    //   };

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
                    {mode === "create" && "Create Category Item"}
                    {mode === "edit" && "Edit Category Item"}
                    {mode === "view" && "View Category Item"}
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
                    <CustomSelect1
                        label="Site"
                        name="site_id"
                        value={data?.site_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.site_id}
                        options={teams}
                    />
                    <CustomDatePicker
                        label="In Use Date"
                        name="in_use_date"
                        mode={mode}
                        value={data?.in_use_date}
                        onChange={handleChange}
                        error={errors.in_use_date}
                    />
                    <CustomDatePicker
                        label="Out Use Date"
                        name="out_use_date"
                        mode={mode}
                        value={data?.out_use_date}
                        onChange={handleChange}
                        error={errors.out_use_date}
                    />
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

export default CategoryItemModal;
