import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";

const SafetyInspectionDetailModal = ({ isOpen, onClose, mode, safetyInspectionDetail = null, sites, inspections, categoryItems, categoryDetails }) => {
    console.log("mode", mode);
    console.log("safetyinspectiondetail", safetyInspectionDetail);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        site_id: null,
        inspection_id: null,
        category_item_id: [],
        category_detail_id: [],
    });
    useEffect(() => {
        if (safetyInspectionDetail && mode !== "create") {
            setData({
                site_id: safetyInspectionDetail.site_id,
                inspection_id: safetyInspectionDetail.inspection_id,
                category_item_id: safetyInspectionDetail.category_item_id,
                category_detail_id: safetyInspectionDetail.category_detail_id,
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,safetyInspectionDetail]);

    const handleChange = (target, value) => {
        setData(target, value);
        //setData("name", e.target.value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("safety-inspection-details.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("safety-inspection-details.update", safetyInspectionDetail.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create SafetyInspectionDetail"}
                    {mode === "edit" && "Edit SafetyInspectionDetail"}
                    {mode === "view" && "View SafetyInspectionDetail"}
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

export default SafetyInspectionDetailModal;
