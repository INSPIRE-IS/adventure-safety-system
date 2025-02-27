import React, { useEffect } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomSelect1 from "@/Components/CustomSelect1";
import CustomDatePicker from "@/Components/CustomDatepicker";
import MultiSelect from "@/Components/MultiSelect";

const DrillDetailModal = ({ isOpen, onClose, mode, drilldetail = null, sites, drills, instructors, signature_files, competents, members }) => {
    console.log("mode", mode);
    console.log("drilldetail", drilldetail);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        site_id: "",
        instructor_id: "",
        drill_id: "",
        signature_file_id: "",
        complete_date: "",
        competent: "",
        memberIds: [],
    });
    useEffect(() => {
        if (drilldetail && mode !== "create") {
            setData({
                site_id: drilldetail.site_id || "",
                instructor_id: drilldetail.instructor_id || "",
                drilldetail_id: drilldetail.drill_id || "",
                signature_file_id: drilldetail.signature_file_id || "",
                complete_date: drilldetail.complete_date || "",
                competent: drilldetail.competent || "",
                memberIds: members.map((member) => member.id),
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
        }
    }, [mode,drilldetail]);

    const handleChange = (target, value) => {
       // setData(target, value);
        //setData("name", e.target.value);
        setData({ ...data, [target]: value });
        console.log("data", data);
    };
    const handleIsChecked = (check_array, check_id) => {
        //console.log("check_array", check_array);
        //console.log("check_id", check_id);
        return Array.isArray(check_array) && check_array.includes(check_id);
    };
    const handleCheckChange = (event,check_id) => {
        //console.log("event", event);
        //console.log("check_id", check_id);
        const isChecked = event.target.checked;
        //console.log("isChecked", isChecked);
        setData((prevState) => {
            const updatedMembers = isChecked
                ? [...prevState.memberIds, check_id]
                : prevState.memberIds.filter((id) => id !== check_id);
        
            return { ...prevState, memberIds: updatedMembers };
        });
    };
    const handleSubmit = (e) => {
        e.preventDefault();
        if (mode === "create") {
            post(route("drill-details.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("drill-details.update", drilldetail.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create DrillDetail"}
                    {mode === "edit" && "Edit DrillDetail"}
                    {mode === "view" && "View DrillDetail"}
                </h2>
                <form onSubmit={handleSubmit}>
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
                        label="Drill"
                        name="drill_id"
                        value={data?.drill_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.drill_id}
                        options={drills}
                    />                    
                    <CustomSelect1
                        label="Instructor"
                        name="instructor_id"
                        value={data?.instructor_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.instructor_id}
                        options={instructors}
                    />
                    <MultiSelect
                        title="Members"
                        fields={members}
                        allFields={data.memberIds}
                        mode={mode}
                        handleIsChecked={handleIsChecked}
                        handleCheckChange={handleCheckChange}
                    />
                    <CustomSelect1
                        label="Signature File"
                        name="signature_file_id"
                        value={data?.signature_file_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.signature_file_id}
                        options={signature_files}
                        />
                    <CustomDatePicker 
                        label="Complete Date"
                        name="complete_date"
                        value={data?.complete_date}
                        mode={mode}
                        onChange={handleChange}
                        error={errors.complete_date}  
                        />
                    <CustomSelect1
                        label="Competent"
                        name="competent"
                        value={data?.competent}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.competent}
                        options={competents}
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

export default DrillDetailModal;
