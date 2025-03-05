import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomSelect1 from "@/Components/CustomSelect1";

const DrillModal = ({ isOpen, onClose, mode, drill = null, sites, typeDrills,categoryItems, periods, actives, users }) => {
    console.log("mode", mode);
    console.log("drill", drill);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        category_item_id: "",
        type_drill_id: "",
        site_id: "",
        period_id: "",
        user_id: "",
        active_id: "",
    });
    useEffect(() => {
        if (drill && mode !== "create") {
            setData({
                name: drill.name || "",
                category_item_id: drill.category_item_id || "",
                type_drill_id: drill.type_drill_id || "",
                site_id: drill.site_id || "",
                period_id: drill.period_id || "",
                user_id: drill.user_id || "",
                active_id: drill.active_id || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,drill]);

    const handleChange = (target, value) => {
        setData(target, value);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("drills.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("drills.update", drill.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Drill"}
                    {mode === "edit" && "Edit Drill"}
                    {mode === "view" && "View Drill"}
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
      
                    {/* <CustomSelect1
                        label="Type Drill"
                        name="type_drill_id"
                        value={data?.type_drill_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_drill_id}
                        options={typeDrills}
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
                    /> */}
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

export default DrillModal;
