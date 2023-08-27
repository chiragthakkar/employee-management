import React, { useState, useEffect } from 'react';

function App() {
  const [employees, setEmployees] = useState([]);
  const [notification, setNotification] = useState(null);
  const [error, setError] = useState(null);


  const handleFileUpload = (event) => {
    const file = event.target.files[0];

    const formData = new FormData();
    formData.append('file', file);

    // Send the file to the server for processing
    fetch('http://localhost:8080/api/upload', {
      method: 'POST',
      body: formData,
    })
    .then(response => (response.json()))
    .then(data => {
      if(data.employees) {
        setEmployees(JSON.parse(data.employees));
        setError(null);
        setNotification(data.message);
      } else {
        setError(data.message);
      }
      event.target.value = null;
    })
    .catch(error => console.error('Error uploading file:', error));
  };

  const handleEmailEdit = (id) => async (event) => {
    const updatedEmail = event.target.value;
    // Update the email in the UI
    const updatedEmployees = employees.map(employee => {
      if (employee.id === id) {
        return {
          ...employee,
          email_address: updatedEmail,
        };
      }
      return employee;
    });
    setEmployees(updatedEmployees);
  };

  const handleEmailBlur = (id) => async (event) => {

    const updatedEmail = event.target.value;

    // Update the email in the Database
    if(/\S+@\S+\.\S+/.test(updatedEmail)) {
      // Todo: we should pass the UUID so that no one can update someone else's email
      try {
        const response = await fetch(`http://localhost:8080/api/update-employee-email`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id: id, email_address: updatedEmail }),
        }).then(response => (response.json()))
        .then(data => {
          if(data.code === 200) {
            setError(null);
            setNotification(data.message);
          } else {
            setError(data.message);
          }
        });
      } catch (error) {
        setError('Error updating email.');
      }
    } else {
      setError('Please provide correct email address.');
    }

    const updatedEmployees = employees.map(employee => {
      if (employee.id === id) {
        return {
          ...employee,
          isEditing: false,
        };
      }
      return employee;
    });
    setEmployees(updatedEmployees);
  };
  

  // reset the success message automatically after 5 seconds
  useEffect(() => {
    setTimeout(() => setNotification(null), 5000);
  }, [employees]);

  useEffect(() => {
    fetch('http://localhost:8080/api/employees')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => setEmployees(data))
      .catch(error => console.error('Error fetching employees:', error));
  }, []);

  // Calculate the average salary for each company
  const averageSalaryByCompany = employees.reduce((accumulator, employee) => {
    if (!accumulator[employee.company_name]) {
      accumulator[employee.company_name] = {
        totalSalary: employee.salary,
        employeeCount: 1,
      };
    } else {
      accumulator[employee.company_name].totalSalary += employee.salary;
      accumulator[employee.company_name].employeeCount += 1;
    }
    return accumulator;
  }, {});

  return (
    <main role="main">
      <div className="container mt-3">
        <h1>Employee List: </h1>
        <p>If you wish to update the email please click on the email to update it:</p>
        <div className="mb-3">
          <input type="file" className="form-control" onChange={handleFileUpload} />
        </div>
        {notification && (
          <div className="alert alert-success" role="alert">
          {notification}
        </div>
        )}
        {error && (
          <div className="alert alert-danger" role="alert">
          {error}
        </div>
        )}
        <table className="table table-striped">
          <thead>
            <tr>
              <th>Name</th>
              <th>Company</th>
              <th>Email</th>
              <th>Salary</th>
            </tr>
          </thead>
          <tbody>
            {employees && employees.map(employee => (
              <tr key={employee.id}>
                <td>{employee.employee_name}</td>
                <td>{employee.company_name}</td>
                <td>
                  {employee.isEditing ? (
                    <input
                      type="email"
                      value={employee.email_address}
                      onChange={handleEmailEdit(employee.id)}
                      onBlur={handleEmailBlur(employee.id)}
                    />
                  ) : (
                    <span
                      onClick={() => {
                        const updatedEmployees = employees.map(emp => {
                          if (emp.id === employee.id) {
                            return {
                              ...emp,
                              isEditing: true,
                            };
                          }
                          return emp;
                        });
                        setEmployees(updatedEmployees);
                      }}
                    >
                      {employee.email_address}
                    </span>
                  )}
                </td>
                <td>${employee.salary.toLocaleString()}</td>
              </tr>
            ))}
          </tbody>
        </table>
        <div className="my-5"></div>

        {/* Display average salary for each company in a separate table */}
        <h2>Average Salary by Company:</h2>
        <table className="table table-striped">
          <thead>
            <tr>
              <th>Company</th>
              <th>Average Salary</th>
            </tr>
          </thead>
          <tbody>
            {Object.keys(averageSalaryByCompany).map(company => (
              <tr key={company}>
                <td>{company}</td>
                <td>
                  ${(
                    averageSalaryByCompany[company].totalSalary /
                    averageSalaryByCompany[company].employeeCount
                  ).toLocaleString()}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </main>
  );
}

export default App;
