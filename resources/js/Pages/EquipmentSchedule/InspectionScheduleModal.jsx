import React, { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import InputText from "@/Components/InputText";
import CustomSelect1 from "@/Components/CustomSelect1";
//import { format } from 'date-fns';
import CustomDatePicker from "@/Components/CustomDatepicker";
import MultiSelect from "@/Components/MultiSelect";
import CustomInput1 from "@/Components/CustomInput1";

const InspectionScheduleModal = ({ auth, isOpen, onClose, mode, inspectionSchedule = null,site, typeActivities, typeInspections,categoryItems, periods, users, type_id }) => {
    console.log("mode", mode);
    console.log("type_id", type_id);
    console.log("categoryItems", categoryItems);
    const [filteredCategoryItems, setFilteredCategoryItems] = useState([]);
    const [type, setType] = useState([]);

    const { data, setData, post, put, errors, reset, processing } = useForm({
        name: "",
        site_id: auth?.team?.id,        
        type_inspection_id: type_id,   
        category_items: categoryItems.map((category_item) => category_item.id) || [],
        period_id: "",
        active_date: "",
        assigned_by_id: auth?.user?.id,
        assigned_to_id: "",

    });
    useEffect(() => {
       if (type_id) {
            const name = type_id == 1 ? 'Safety' : 'PPE';

            setType(name);
        }
        if (inspectionSchedule && mode !== "create") {
            setData({
                name: inspectionSchedule.name || "",
                category_item_id: inspectionSchedule.category_item_id || "",
                category_items: inspectionSchedule.category_items.map((category_item) => category_item.id) || [], //inspectionSchedule.category_items.map((category_item) => category_item.id),
                type_inspection_id: inspectionSchedule.type_inspection_id || "",
                site_id: inspectionSchedule.site_id || "",
                period_id: inspectionSchedule.period_id || "",
                assigned_to_id: inspectionSchedule.assigned_to_id || "",
                active_date: inspectionSchedule.active_date || "",
            });
        } else if (mode === "create") {
            reset(); // Clear form for create mode
            // setData({
            //     name: auth?.team?.name || "",
            // });
        }
    }, [mode,inspectionSchedule,type_id]);

    useEffect(() => {
        if (categoryItems) {// data.type_inspection_id
            const filteredItems = categoryItems.filter(
                (item) => item.type_inspection_id == 3 // data.type_inspection_id
            );
            setFilteredCategoryItems(filteredItems);
        } else {
            setFilteredCategoryItems([]);
        }
    }, [ ]);// data.type_inspection_id,

    function formatDate(isoDate) {
        const date = new Date(isoDate);
        //console.log('date', date); return;
        const day = String(date.getDate()).padStart(2, '0');   // Get day and pad with leading zero
        const month = String(date.getMonth() + 1).padStart(2, '0');  // Get month (months are 0-indexed, so add 1) and pad
        const year = date.getFullYear();   // Get full year
    
        return `${day}-${month}-${year}`;  // Return formatted date as DD-MM-YYYY
    }

    const handleChange = (target, value) => {
        //const formattedValue = target === 'active_date' ? format(value, 'dd-MM-yyyy') : value;
        console.log(value);
        //console.log(formattedValue);
        //setData(target, formattedValue);
        setData({ ...data, [target]: value });
        console.log("data", data);
        //setData("name", e.target.value);
    };

    const handleIsChecked = (check_array, check_id) => {
        console.log("check_array", check_array);
        console.log("check_id", check_id);
        return Array.isArray(check_array) && check_array.includes(check_id);
    };
    const handleCheckChange = (event,check_id) => {
        console.log("event", event);
        console.log("check_id", check_id);
        const isChecked = event.target.checked;
        console.log("isChecked", isChecked);
        setData((prevState) => {
            const updatedCategoryItems = isChecked
                ? [...prevState.category_items, check_id]
                : prevState.category_items.filter((id) => id !== check_id);
        
            return { ...prevState, category_items: updatedCategoryItems };
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log("data", data);
        //return;
        if (mode === "create") {
            post(route("inspection-schedules.store"), {

                onSuccess: () => {reset();onClose()},
            });
        } else if (mode === "edit") {
            put(route("inspection-schedules.update", inspectionSchedule.id), {
                onSuccess: () => {reset();onClose()},
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white rounded-lg shadow-lg p-6 w-full max-w-md max-h-[95vh] overflow-y-auto">
                <h2 className="text-xl font-bold mb-4">
                    {mode === "create" && "Create " + type + " Inspection Schedule"}
                    {mode === "edit" && "Edit " + type + " Inspection Schedule"}
                    {mode === "view" && "View " + type + " Inspection Schedule"}
                </h2>
                <form onSubmit={handleSubmit}>               
                    <label className="block text-lg font-semibold mb-4">
                    {site}
                    </label>
                    <CustomSelect1
                        label="Activity"
                        name="name"
                        value={data?.name}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.name}
                        options={typeActivities}
                    />
                    {/* <CustomSelect1
                        label="Type Inspection"
                        name="type_inspection_id"
                        value={data?.type_inspection_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.type_inspection_id}
                        options={typeInspections}
                    />*/}
                    <div className="mb-4">
                        <label className="block text-sm font-medium text-gray-700">
                        Equipment
                        </label>
                        <div
                            className="mt-1 border border-gray-300 rounded-md p-2 w-full overflow-y-scroll flex flex-wrap gap-2"
                        >
                            {filteredCategoryItems?.map((category_item) => (
                                <label
                                    key={category_item.id}
                                    className="flex items-center text-sm space-x-2"
                                >
                                    <input
                                        type="checkbox"
                                        value={category_item.id}
                                        checked={Array.isArray(data.category_items) && data.category_items.includes(category_item.id)}
                                        onChange={(e) => {
                                            const isChecked = e.target.checked;
                                            setData((prevState) => {
                                                const updatedItems = isChecked
                                                    ? [...prevState.category_items, category_item.id]
                                                    : prevState.category_items.filter((id) => id !== category_item.id);
                                            
                                                return { ...prevState, category_items: updatedItems };
                                            });
                                            }
                                        }
                                        className="form-checkbox h-4 w-4 text-blue-600"
                                        disabled={mode === "view"}
                                    />
                                    <span>{category_item.name}</span>
                                </label>
                            ))}
                        </div>
                    </div> 
                    <CustomSelect1
                        label="Assessment Period (set to none to stop the cron job)"
                        name="period_id"
                        value={data?.period_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.period_id}
                        options={periods}
                    /> 
                    <CustomInput1
                        label="Active Date (cron job runs from this date by specified period)"
                        name="active_date"
                        type="date"
                        value={data?.active_date}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.active_date}  
                    />
                    <CustomSelect1
                        label="User"
                        name="assigned_to_id"
                        value={data?.assigned_to_id}
                        mode={mode}
                        onChange={(e) => handleChange(e.target.name, e.target.value)}
                        error={errors.assigned_to_id}
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

export default InspectionScheduleModal;
