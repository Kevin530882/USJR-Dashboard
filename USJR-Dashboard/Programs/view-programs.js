let currentRow = null;
let editID = document.getElementById('editID');
let editFname = document.getElementById('editFname');
let editSname = document.getElementById('editSname');
let editCollege = document.getElementById('progcollid');
let editDepartment = document.getElementById('progdeptid');
let editPanel = document.querySelector('.edit-panel');

function closeme() {
    editPanel.style.display = "none";
}

axios.get('../get-colleges-programs.php')
.then(response => {
    console.log(response.data);
    const tablebody = document.getElementById('table-body');
    const colleges = response.data.colleges;
    const programs = response.data.programs;
    const departments = response.data.departments;

    programs.forEach(program => {
        const tr = document.createElement('tr');
        const edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="fill: green" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>';
        const del = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="fill: red" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/></svg>';
        let coll; let colid;
        let dept; let depid;
        colleges.forEach(college => {
            if(college.collid == program.progcollid) {
                coll = college.collfullname;
                colid = college.collid
            }
        })
        departments.forEach(department => {
            if(department.deptid == program.progcolldeptid){
                dept = department.deptfullname;
                depid = department.deptid;
            }
        })

        tr.innerHTML = `
            <td class="iamid">${program.progid}</td>
            <td>${program.progfullname}</td>
            <td>${program.progshortname}</td>
            <td>${coll}</td>
            <td>${dept}</td>
            <td>
                <button class="btn btn-light editprog">${edit}</button>
                <button class="btn btn-light deleteprog">${del}</button>
            </td>
        `;
        tablebody.appendChild(tr);

        tr.querySelector('.editprog').addEventListener('click', () => {
            currentRow = tr;
            editID.value = program.progid;
            editFname.value = program.progfullname;
            editSname.value = program.progshortname;

            editPanel.style.display = 'block';

            let collegeOption = document.createElement('option');
            collegeOption.value = colid;
            collegeOption.textContent = coll;
            editCollege.appendChild(collegeOption);

            let departmentOption = document.createElement('option');
            departmentOption.value = depid;
            departmentOption.textContent = dept;
            editDepartment.appendChild(departmentOption);

            editPanel.style.display = "block";
            fetchCollegesAndDepartments(colid, depid);
        });
        tr.querySelector('.deleteprog').addEventListener('click', () => {
            currentRow = tr;
            const id = currentRow.querySelector('.iamid').innerText;

            Swal.fire({
                title: "Are you sure?",
                text: "This action will permanently delete the college.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                allowEscapeKey: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('editProgram.php', {
                        id: id,
                        action: "delete"
                    })
                    .then(response => {
                        console.log(response.data);
                        if (response.data.status === 'success') {
                            currentRow.remove();
                            currentRow = null;
                            Swal.fire({
                                icon: "success",
                                title: "Program deleted successfully",
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Delete Failed",
                                text: response.data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error: ', error);
                    });
                }
            })
        })
    });
})
.catch(error => {
    console.error('Error fetching programs:', error);
});

let edit = document.querySelector('.saveedit');
edit.addEventListener('click', () => {
    if (currentRow) {
        const id = editID.value;
        const fname = editFname.value;
        const sname = editSname.value;
        const college = editCollege.value;
        const department = editDepartment.value;

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to save the changes?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, save it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('editProgram.php', {
                    id: id,
                    fname: fname,
                    sname: sname,
                    college: college,
                    department: department,
                    action: "edit"
                })
                .then(response => {
                    console.log(response.data);
                    if (response.data.status === 'success') {
                        console.log("Update successful: ", response.data.message);
                        currentRow.querySelector('td:nth-child(2)').innerText = fname;
                        currentRow.querySelector('td:nth-child(3)').innerText = sname;
                        currentRow.querySelector('td:nth-child(4)').innerText = editCollege.textContent;
                        currentRow.querySelector('td:nth-child(5)').innerText = editDepartment.textContent;
            
                        Swal.fire({
                            icon: "success",
                            title: "Program updated successfully",
                            timer: 3000,
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            timerProgressBar: true
                        }).then(() => {
                            editPanel.style.display = "none";
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Update Failed",
                            text: response.data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred while updating the program"
                    });
                    console.error('Error: ', error);
                });
            }
        })
    }
});

function fetchCollegesAndDepartments(selectedCollegeId, selectedDepartmentId) {
    axios.get('../get-colleges-programs.php')
    .then(response => {
        console.log(response.data); 
        const colleges = response.data.colleges;
        const departments = response.data.departments;

        colleges.forEach(college => {
            const option = document.createElement('option');
            option.value = college.collid;
            option.textContent = college.collfullname;
            editCollege.appendChild(option);
        });

        editCollege.addEventListener('change', () => {
            editDepartment.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select Department';
            editDepartment.appendChild(defaultOption);

            const selectedCollegeId = editCollege.value;
            departments.forEach(department => {
                if (department.deptcollid == selectedCollegeId) {
                    const option = document.createElement('option');
                    option.value = department.deptid;
                    option.textContent = department.deptfullname;
                    editDepartment.appendChild(option);
                }
            });
        });
        if (selectedCollegeId) {
            editCollege.value = selectedCollegeId;
            editCollege.dispatchEvent(new Event('change'));
        }
        if (selectedDepartmentId) {
            editDepartment.value = selectedDepartmentId;
        }
    })
    .catch(error => {
        console.error('Error fetching colleges and departments:', error);
    });
}