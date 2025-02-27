const EditButton = ({title,icon,width, height, onClick }) => {
    return (
        <button
            className={` px-4 py-2 ${width} ${height} border border-[#D0D0D0] rounded-xl hover:bg-[#f8bf2d] hover:text-black`}

            onClick={onClick}
        >
            <div className="w-full h-full flex flex-row items-center justify-evenly">
                <img src={icon} alt="Icon" width={19} height={19} />                
                <h4 className="button-text-edit">{title}</h4>

            </div>
        </button>
    );
};

export default EditButton;
