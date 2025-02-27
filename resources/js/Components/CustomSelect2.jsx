import React from "react";

const CustomSelect2 = ({ label, name, value, onChange, error,mode, options }) => {
  return (
    <div className="w-[235px] h-[56px] mb-4">
      <select
        name={name}
        value={value}
        onChange={onChange}
        className={`mt-1 block w-full h-full bg-[#E4E4E4] border-gray-300 shadow-sm input-text rounded-2xl`}
        disabled={mode === "view"}

      >
        {options.map((option, index) => (
          <option key={index} value={option.value} className="input-text">
            {option.label}
          </option>
        ))}
      </select>
    </div>
  );
};
        // className={`w-full pl-[19px] pt-[11px] pb-[12px] bd-[#FBF9F7] border rounded-full focus:outline-none focus:ring-2 ${
        //   error
        //     ? "border-red-500 focus:ring-red-300 text-red-700"
        //     : "border-gray-300 focus:ring-blue-300 text-main2"
        // }`}
        // style={{
        //     backgroundColor: "#FBF9F7", // Explicitly set the background color in case Tailwind is overridden
        //   }}
export default CustomSelect2;
