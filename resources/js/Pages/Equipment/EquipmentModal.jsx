import React, { useEffect, useState } from "react";
import { router, useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomButton from "@/Components/CustomButton";

const EquipmentModal = ({ isOpen, onClose, mode, equipmentItem = null, team }) => {
    const { data, setData, post, put, errors, reset } = useForm({
        name: "",
        type_inspection_id: 3,
        site_id: team?.id,
        in_use_date: new Date().toISOString().split("T")[0],
        out_use_date: "",
        certificate_recieved_id: 2,
        identifications: [], // Stores barcodes or numbered items
    });

    const [newIdentification, setNewIdentification] = useState("");

    useEffect(() => {
        if (equipmentItem && mode !== "create") {
            setData({
                name: equipmentItem.name || "",
                type_inspection_id: equipmentItem.type_inspection_id || "",
                site_id: equipmentItem.site_id || "",
                in_use_date: equipmentItem.in_use_date || "",
                out_use_date: equipmentItem.out_use_date || "",
                certificate_recieved_id: equipmentItem.certificate_recieved_id || "",
                identifications: equipmentItem.identifications || [], // Load existing identifications
            });
        } else if (mode === "create") {
            reset();
        }
    }, [mode, equipmentItem]);

    const handleChange = (target, value) => {
        setData(target, value);
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

    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("category-items.store"), {
                onSuccess: () => {
                    reset();
                    onClose();
                },
            });
        } else if (mode === "edit") {
            put(route("category-items.update", equipmentItem.id), {
                onSuccess: () => {
                    reset();
                    onClose();
                },
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl max-h-[95%] overflow-y-auto">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create Equipment & Item #"}
                    {mode === "edit" && "Edit Equipment & Item #"}
                    {mode === "view" && "View Equipment & Item #"}
                </h2>
                <form onSubmit={handleSubmit}>
                    <InputText
                        title="Equipment Name"
                        field="name"
                        value={data.name}
                        mode={mode}
                        handleChange={handleChange}
                        errors={errors}
                    />

                    {/* Identification Section */}
                    <div className="mt-4">
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

export default EquipmentModal;
