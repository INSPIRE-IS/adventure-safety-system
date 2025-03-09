const EditButton2 = ({ title, icon = null, width, height, onClick }) => {
    return (
      <button 
        className={`px-4 py-2 ${width} ${height} border border-[#D0D0D0] rounded-xl hover:bg-[#f8bf2d] hover:text-black`}
        onClick={onClick}
      >
        <div className="w-full h-full flex flex-row items-center justify-evenly">
          {icon && <>{icon}</>} {/* ✅ Render icon properly */}
          <span className="button-text-edit">{title}</span>
        </div>
      </button>
    );
  };
  
  export default EditButton2;