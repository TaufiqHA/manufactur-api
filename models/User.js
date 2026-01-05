const { query, run } = require('../config/db');
const bcrypt = require('bcryptjs');

class User {
  static async findAll() {
    const result = await query('SELECT id, name, username, role, permissions FROM users ORDER BY name');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT id, name, username, role, permissions FROM users WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByUsername(username) {
    const result = await query('SELECT id, name, username, role, permissions, password FROM users WHERE username = ?', [username]);
    return result.rows[0];
  }

  static async create(userData) {
    const { id, name, username, role, permissions, password } = userData;

    // Check if password is provided and is a string
    if (!password || typeof password !== 'string') {
      throw new Error('Password is required and must be a string');
    }

    const hashedPassword = await bcrypt.hash(password, 10);
    await run(`
      INSERT INTO users (id, name, username, role, permissions, password)
      VALUES (?, ?, ?, ?, ?, ?)
    `, [id, name, username, role, JSON.stringify(permissions), hashedPassword]);

    return this.findById(id);
  }

  static async update(id, userData) {
    const { name, username, role, permissions, password } = userData;
    if (password) {
      // Check if password is a string when provided
      if (typeof password !== 'string') {
        throw new Error('Password must be a string');
      }
      const hashedPassword = await bcrypt.hash(password, 10);
      await run(`
        UPDATE users
        SET name = ?, username = ?, role = ?, permissions = ?, password = ?
        WHERE id = ?
      `, [name, username, role, JSON.stringify(permissions), hashedPassword, id]);
    } else {
      await run(`
        UPDATE users
        SET name = ?, username = ?, role = ?, permissions = ?
        WHERE id = ?
      `, [name, username, role, JSON.stringify(permissions), id]);
    }

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM users WHERE id = ?', [id]);
    return true;
  }

  static async validatePassword(username, password) {
    const user = await this.findByUsername(username);
    if (!user) return false;

    const isValid = await bcrypt.compare(password, user.password);
    return isValid ? user : false;
  }
}

module.exports = User;