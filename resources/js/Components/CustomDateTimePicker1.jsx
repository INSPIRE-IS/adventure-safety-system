import React, { useEffect, useState } from "react";
// import DatePicker from "react-date-picker";
// import TimePicker from "react-time-picker"; // Import Time Picker
// import "react-date-picker/dist/DatePicker.css"; // Default styles
// import "react-time-picker/dist/TimePicker.css"; // Default styles

const CustomDateTimePicker = ({ 
  label, 
  name, 
  value, 
  onChange, 
  mode, 
  type = "date", // "date" or "time"
  error 
}) => {
  const [selectedValue, setSelectedValue] = useState("");

  useEffect(() => {
    if (value && mode !== "create") {
      setSelectedValue(value);
    }
  }, [mode, value]);

  const handleChange = (newValue) => {
    setSelectedValue(newValue);
    onChange(name, newValue); // Pass the selected value to the parent
  };

  return (
    <div className="w-full mb-4">
      {label && <label className="block input-header mb-[14px]">{label}</label>}
      

      {error && <p className="text-red-500 text-sm mt-1">{error}</p>}
    </div>
  );
};

export default CustomDateTimePicker;
