import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import axios from "axios";

//to get the csv data
export const fetchHealthData = createAsyncThunk("health/fetchData", async () => {
  const response = await axios.get("/api/health-data", {
    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
  });
  return response.data;
});

//to upload the csv data
export const uploadCSV = createAsyncThunk("health/uploadCSV", async (file) => {
  const formData = new FormData();
  formData.append("file", file);
  await axios.post("/api/upload", formData, {
    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
  });
});

const healthSlice = createSlice({
  name: "health",
  initialState: { data: [], loading: false },
  reducers: {},
  extraReducers: (builder) => {
    builder.addCase(fetchHealthData.fulfilled, (state, action) => {
      state.data = action.payload;
    });
  },
});

export default healthSlice.reducer;
