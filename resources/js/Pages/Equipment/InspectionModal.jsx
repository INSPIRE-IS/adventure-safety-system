import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomSelect1 from "@/Components/CustomSelect1";

const InspectionModal = ({ isOpen, onClose, mode, inspection = null, sites, typeInspections,categoryItems, periods, actives, users }) => {
    console.log("mode", mode);
    console.log("inspection", inspection);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        category_item_id: "",
        type_inspection_id: "",
        site_id: "",
        period_id: "",
        user_id: "",
        active_id: "",
    });
    useEffect(() => {
        if (inspection && mode !== "create") {
            setData({
                name: inspection.name || "",
                category_item_id: inspection.category_item_id || "",
                type_inspection_id: inspection.type_inspection_id || "",
                site_id: inspection.site_id || "",
                period_id: inspection.period_id || "",
                user_id: inspection.user_id || "",
                active_id: inspection.active_id || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,inspection]);

    const handleChange = (target, value) => {
        setData(target, value);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("inspections.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("inspections.update", inspection.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Inspection"}
                    {mode === "edit" && "Edit Inspection"}
                    {mode === "view" && "View Inspection"}
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
                        label="Site"
                        name="site_id"
                        value={data?.site_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.site_id}
                        options={sites}
                    />
                    <CustomSelect1
                        label="Category"
                        name="category_item_id"
                        value={data?.category_item_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.category_item_id}
                        options={categoryItems}
                    />        
                    <CustomSelect1
                        label="Type Inspection"
                        name="type_inspection_id"
                        value={data?.type_inspection_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_inspection_id}
                        options={typeInspections}
                    />                     
                    <CustomSelect1
                        label="Assessment Period"
                        name="period_id"
                        value={data?.period_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.period_id}
                        options={periods}
                    /> 
                    <CustomSelect1
                        label="Active"
                        name="active_id"
                        value={data?.active_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.active_id}
                        options={actives}
                    /> 
                    <CustomSelect1
                        label="User"
                        name="user_id"
                        value={data?.user_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.user_id}
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

export default InspectionModal;
