import React from "react";

const CustomInput1 = ({ label, type = "text", name, value, onChange, error, placeholder }) => {
  return (
    <div className="w-full mb-4">
      {label && (
        <label className="block text-main mb-[14px]">
          {label}
        </label>
      )}
      <input
        id={name}
        name={name}
        type={type}        
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        className={`w-full pl-[19px] pt-[11px] pb-[12px] bg-[#FBF9F7] border rounded-full focus:outline-none focus:ring-2 ${
          error
            ? "border-red-500 focus:ring-red-300 text-red-700"
            : "border-gray-300 focus:ring-blue-300 text-main2"
        }`}
      />
      {error && <p className="text-red-500 text-sm mt-1">{error}</p>}
    </div>
  );
};

export default CustomInput1;
