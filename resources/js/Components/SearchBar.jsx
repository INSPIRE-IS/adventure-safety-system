const SearchBar = ({ search, handleSearchChange, handleSearchSubmit }) => {
    return (
        <div className="w-[100%] lg:w-[433px] lg:h-[67px] flex items-center justify-between relative">
            <input
                type="text"
                placeholder="Enter Search Field"
                value={search}
                onChange={(e) => handleSearchChange(e)}
                className="w-full h-full search-text1  p-2 pl-10 border rounded-xl" // Add padding-left to create space for the icon
                //onChange={(e) => onSearch(e.target.value)}
            />
            <img
                src="./images/search-icon.svg"
                alt="Search Icon"
                className="w-5 h-5 absolute right-3 top-1/2 transform -translate-y-1/2" // Position the icon inside the input
                onClick={(search) => handleSearchSubmit(search)}
            />
        </div>

    );
};

export default SearchBar;
